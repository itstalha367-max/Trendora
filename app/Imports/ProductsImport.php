<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find or create category
        $category = Category::firstOrCreate(
            ['name' => $row['category']],
            ['slug' => Str::slug($row['category']), 'status' => true]
        );

        return new Product([
            'name' => $row['name'],
            'slug' => Str::slug($row['name']),
            'category_id' => $category->id,
            'description' => $row['description'] ?? '',
            'price' => $row['price'],
            'compare_price' => $row['compare_price'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? 0,
            'sku' => $row['sku'] ?? null,
            'status' => $row['status'] ?? true,
            'featured' => $row['featured'] ?? false,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ];
    }
}