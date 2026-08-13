<?php
namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookDispatcher
{
    public function dispatch(string $event, mixed $resource, array $extra = []): void
    {
        $webhooks = Webhook::where('status', true)->get()->filter(fn($webhook) => in_array($event, $webhook->events ?: [], true));
        if ($webhooks->isEmpty()) return;

        $payload = [
            'id' => (string) Str::uuid(),
            'event' => $event,
            'created_at' => now()->toIso8601String(),
            'data' => $this->serializeResource($resource),
            'meta' => $extra,
        ];

        foreach ($webhooks as $webhook) {
            if (!$this->isPublicHttpsUrl($webhook->url)) continue;
            $delivery = $webhook->deliveries()->create([
                'event' => $event,
                'payload' => $payload,
                'status' => 'pending',
                'attempted_at' => now(),
                'attempt_count' => 0,
            ]);
            $this->deliver($delivery);
        }
    }

    public function deliver(WebhookDelivery $delivery): bool
    {
        $webhook = $delivery->webhook;
        if (!$webhook || !$webhook->status || !$this->isPublicHttpsUrl($webhook->url)) {
            $delivery->update(['status' => 'failed', 'response_body' => 'Webhook disabled or destination is not a public HTTPS URL.']);
            return false;
        }

        $payload = $delivery->payload ?: [];
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $body ?: '', $webhook->secret ?? '');
        $attempt = (int) $delivery->attempt_count + 1;

        try {
            $response = Http::timeout(5)->connectTimeout(3)->withHeaders([
                'User-Agent' => 'Trendora-Webhooks/1.0',
                'X-Trendora-Event' => $delivery->event,
                'X-Trendora-Delivery' => (string) $delivery->id,
                'X-Trendora-Signature' => 'sha256='.$signature,
                'Content-Type' => 'application/json',
            ])->withBody($body ?: '{}', 'application/json')->post($webhook->url);

            $success = $response->successful();
            $delivery->update([
                'attempt_count' => $attempt,
                'attempted_at' => now(),
                'response_code' => $response->status(),
                'response_body' => Str::limit($response->body(), 2000),
                'status' => $success ? 'delivered' : 'failed',
                'delivered_at' => $success ? now() : null,
                'next_retry_at' => $success ? null : now()->addMinutes(min(60, 5 * $attempt)),
            ]);
            $webhook->update(['last_triggered_at' => now()]);
            return $success;
        } catch (\Throwable $e) {
            $delivery->update([
                'attempt_count' => $attempt,
                'attempted_at' => now(),
                'status' => 'failed',
                'response_body' => Str::limit($e->getMessage(), 2000),
                'next_retry_at' => now()->addMinutes(min(60, 5 * $attempt)),
            ]);
            return false;
        }
    }

    private function serializeResource(mixed $resource): mixed
    {
        if ($resource instanceof Model) {
            $array = $resource->toArray();
            foreach (['password','remember_token','two_factor_secret','encrypted_secret','payment_data'] as $sensitive) unset($array[$sensitive]);
            return $array;
        }
        if (is_array($resource)) return $resource;
        return ['value' => $resource];
    }

    private function isPublicHttpsUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])) return false;
        $host = $parts['host'];
        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
