<?php
namespace App\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function deduct(Product $product, ?ProductVariation $variation, int $quantity, string $reference): ?int
    {
        $query = Inventory::where('product_id', $product->id)
            ->where('product_variation_id', $variation?->id)
            ->lockForUpdate();
        $locations = $query->orderByRaw('(quantity - reserved_quantity) DESC')->get();

        if ($locations->isNotEmpty()) {
            $inventory = $locations->first(fn($row) => max(0, $row->quantity - $row->reserved_quantity) >= $quantity);
            if (!$inventory) {
                throw ValidationException::withMessages(['cart' => $product->name . ' does not have enough stock in a single fulfilment location.']);
            }
            $before = $inventory->quantity;
            $inventory->decrement('quantity', $quantity);
            $inventory->movements()->create([
                'user_id' => auth()->id(), 'type' => 'sale', 'quantity' => -$quantity,
                'before_quantity' => $before, 'after_quantity' => $before - $quantity,
                'reference' => $reference, 'note' => 'Order stock deduction',
            ]);
            $this->syncLegacyStock($product, $variation);
            return $inventory->warehouse_id;
        }

        if ($variation) {
            $locked = ProductVariation::whereKey($variation->id)->lockForUpdate()->firstOrFail();
            if ($locked->stock_quantity < $quantity) throw ValidationException::withMessages(['cart' => $product->name . ' does not have enough stock.']);
            $locked->decrement('stock_quantity', $quantity);
        } else {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            if ($locked->stock_quantity < $quantity) throw ValidationException::withMessages(['cart' => $product->name . ' does not have enough stock.']);
            $locked->decrement('stock_quantity', $quantity);
        }
        return null;
    }

    public function restoreOrder(Order $order, string $reason = 'Order cancelled'): void
    {
        $order->loadMissing('items.product');
        foreach ($order->items as $item) {
            $inventory = $item->warehouse_id
                ? Inventory::where('warehouse_id', $item->warehouse_id)->where('product_id', $item->product_id)->where('product_variation_id', $item->product_variation_id)->lockForUpdate()->first()
                : null;
            if ($inventory) {
                $before = $inventory->quantity;
                $inventory->increment('quantity', $item->quantity);
                $inventory->movements()->create([
                    'user_id' => auth()->id(), 'type' => 'return', 'quantity' => $item->quantity,
                    'before_quantity' => $before, 'after_quantity' => $before + $item->quantity,
                    'reference' => $order->order_number, 'note' => $reason,
                ]);
                $this->syncLegacyStock($item->product, $item->variation);
            } elseif ($item->product_variation_id) {
                ProductVariation::whereKey($item->product_variation_id)->increment('stock_quantity', $item->quantity);
            } else {
                Product::whereKey($item->product_id)->increment('stock_quantity', $item->quantity);
            }
        }
    }

    private function syncLegacyStock(Product $product, ?ProductVariation $variation): void
    {
        if ($variation) {
            $sum = Inventory::where('product_id', $product->id)->where('product_variation_id', $variation->id)->sum('quantity');
            $variation->update(['stock_quantity' => $sum]);
        } else {
            $sum = Inventory::where('product_id', $product->id)->whereNull('product_variation_id')->sum('quantity');
            $product->update(['stock_quantity' => $sum]);
        }
    }
}
