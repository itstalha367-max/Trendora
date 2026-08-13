<?php
namespace App\Services;

use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\TaxRate;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CommerceCalculator
{
    public function shippingMethods(string $country, ?string $state, float $subtotal): Collection
    {
        $country = trim($country);
        $state = trim((string) $state);

        $zones = ShippingZone::with(['methods' => fn($q) => $q->where('status', true)->orderBy('cost')])
            ->where('status', true)->get();

        $matched = $zones->filter(fn($zone) => $this->zoneMatches($zone, $country, $state));
        $methods = $matched->flatMap->methods->map(function ($method) use ($subtotal) {
            $cost = (float) $method->cost;
            if ($method->type === 'free' || ($method->free_over !== null && $subtotal >= (float) $method->free_over)) {
                $cost = 0;
            }
            return [
                'id' => $method->id,
                'name' => $method->name,
                'type' => $method->type,
                'cost' => round($cost, 2),
                'min_days' => $method->min_days,
                'max_days' => $method->max_days,
                'zone' => $method->zone?->name,
            ];
        })->values();

        if ($methods->isEmpty() && Setting::get('shipping_fallback_enabled', 'on') === 'on') {
            $methods = collect([[
                'id' => null,
                'name' => Setting::get('default_shipping_name', 'Standard delivery'),
                'type' => 'flat_rate',
                'cost' => (float) Setting::get('default_shipping_cost', 0),
                'min_days' => (int) Setting::get('default_shipping_min_days', 3),
                'max_days' => (int) Setting::get('default_shipping_max_days', 7),
                'zone' => 'Default',
            ]]);
        }

        return $methods;
    }

    public function quote(float $subtotal, float $discount, string $country, ?string $state, ?int $shippingMethodId, bool $freeShipping = false): array
    {
        $methods = $this->shippingMethods($country, $state, $subtotal);
        $method = $shippingMethodId
            ? $methods->firstWhere('id', $shippingMethodId)
            : $methods->first();

        if (!$method) {
            throw ValidationException::withMessages(['shipping_method_id' => 'Selected shipping method is not available for this address.']);
        }

        $shipping = $freeShipping ? 0.0 : (float) $method['cost'];
        if ($freeShipping) { $method['cost'] = 0.0; $method['name'] .= ' · Promotion'; }
        $taxableSubtotal = max(0, $subtotal - $discount);
        $taxRates = $this->resolveTaxRates($country, $state);
        $tax = 0.0; $taxNames = []; $nominalRate = 0.0;
        foreach ($taxRates as $rate) {
            $base = $taxableSubtotal + ($rate->shipping_taxable ? $shipping : 0) + ($rate->compound ? $tax : 0);
            $tax += round($base * ((float) $rate->rate / 100), 2);
            $taxNames[] = $rate->name; $nominalRate += (float) $rate->rate;
        }
        $tax = round($tax, 2);
        $total = max(0, round($taxableSubtotal + $shipping + $tax, 2));

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'shipping' => round($shipping, 2),
            'shipping_method' => $method,
            'tax' => $tax,
            'tax_name' => $taxNames ? implode(' + ', $taxNames) : null,
            'tax_rate' => $nominalRate,
            'total' => $total,
        ];
    }

    private function resolveTaxRates(string $country, ?string $state): Collection
    {
        $country = trim($country);
        $state = trim((string) $state);
        return TaxRate::where('status', true)->orderBy('priority')->get()->filter(function ($tax) use ($country, $state) {
            $countryOk = !$tax->country || $this->sameLocation($tax->country, $country);
            $stateOk = !$tax->state || $this->sameLocation($tax->state, $state);
            return $countryOk && $stateOk;
        })->values();
    }

    private function zoneMatches(ShippingZone $zone, string $country, string $state): bool
    {
        $countries = $zone->countries ?: [];
        $states = $zone->states ?: [];
        $countryOk = empty($countries) || collect($countries)->contains(fn($value) => $this->sameLocation($value, $country));
        $stateOk = empty($states) || collect($states)->contains(fn($value) => $this->sameLocation($value, $state));
        return $countryOk && $stateOk;
    }

    private function sameLocation(?string $a, ?string $b): bool
    {
        $normalize = fn($value) => strtolower(preg_replace('/[^a-z0-9]/i', '', trim((string) $value)));
        $a = $normalize($a); $b = $normalize($b);
        if ($a === '' || $b === '') return false;
        if ($a === $b) return true;
        $aliases = ['pakistan'=>'pk','unitedstates'=>'us','usa'=>'us','unitedkingdom'=>'gb','uk'=>'gb','uae'=>'ae','unitedarabemirates'=>'ae','saudiarabia'=>'sa'];
        return ($aliases[$a] ?? $a) === ($aliases[$b] ?? $b);
    }
}
