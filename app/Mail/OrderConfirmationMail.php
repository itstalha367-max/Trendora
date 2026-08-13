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

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customMessage;
    private $customSubject;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $template = EmailTemplate::where('key','order_confirmation')->where('status',true)->first();
        $vars=['{{customer_name}}'=>$order->user?->name ?? $order->shipping_name ?? 'Customer','{{order_number}}'=>$order->order_number,'{{order_total}}'=>number_format($order->total,2),'{{tracking_number}}'=>$order->tracking_number ?? ''];
        $this->customSubject=$template?strtr($template->subject,$vars):null;
        $this->customMessage=$template?strtr($template->content,$vars):null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject ?: 'Order Confirmation - #' . ($this->order->order_number ?? $this->order->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}