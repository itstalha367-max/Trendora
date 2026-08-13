<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Description',
            'Price',
            'Compare Price',
            'Stock',
            'SKU',
            'Status',
            'Featured',
            'Created At'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category->name ?? 'N/A',
            $product->description,
            $product->price,
            $product->compare_price,
            $product->stock_quantity,
            $product->sku,
            $product->status ? 'Active' : 'Inactive',
            $product->featured ? 'Yes' : 'No',
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}