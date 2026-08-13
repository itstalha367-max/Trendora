<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;
    public $customMessage;
    private $customSubject;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;

        if ($newStatus === 'shipped') {
            $template = EmailTemplate::query()->where('key', 'order_shipped')->where('status', true)->first();
            if ($template) {
                $replace = [
                    '{{customer_name}}' => $order->user?->name ?? 'Customer',
                    '{{order_number}}' => $order->order_number ?? (string) $order->id,
                    '{{order_total}}' => number_format((float) $order->total, 2),
                    '{{tracking_number}}' => $order->tracking_number ?: 'Will be shared shortly',
                ];
                $this->customSubject = strtr($template->subject, $replace);
                $this->customMessage = strtr($template->content, $replace);
            }
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject ?: 'Order Status Updated - #' . ($this->order->order_number ?? $this->order->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}