<?php

namespace App\Services;

use App\Models\NotificationTemplate;

class TemplateRenderer
{
    /**
     * Render a database notification template with safe fallback copy.
     * Variables are passed without braces, e.g. ['order_number' => 'TR-1001'].
     */
    public function notification(
        string $key,
        array $variables,
        string $fallbackTitle,
        string $fallbackMessage
    ): array {
        $template = NotificationTemplate::query()
            ->where('key', $key)
            ->where('status', true)
            ->first();

        $replace = [];
        foreach ($variables as $name => $value) {
            $replace['{{'.$name.'}}'] = (string) $value;
        }

        return [
            'title' => strtr($template?->title ?: $fallbackTitle, $replace),
            'message' => strtr($template?->content ?: $fallbackMessage, $replace),
        ];
    }
}
