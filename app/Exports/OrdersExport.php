<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Customer',
            'Email',
            'Total',
            'Status',
            'Payment Status',
            'Order Date',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number ?? $order->id,
            $order->user->name ?? 'Guest',
            $order->user->email ?? 'N/A',
            $order->total,
            $order->order_status,
            $order->payment_status,
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }
}