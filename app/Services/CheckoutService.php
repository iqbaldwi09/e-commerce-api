<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function getHistory($userId)
    {
        return Checkout::with('items.product')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function checkout(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            $checkout = Checkout::create([
                'user_id' => $userId,
                'total'   => 0,
                'status'  => 'pending',
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $product  = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];

                CheckoutItem::create([
                    'checkout_id' => $checkout->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'price'       => $product->price,
                ]);

                $total += $subtotal;

                $product->decrement('stock', $item['quantity']);
            }

            $checkout->update(['total' => $total]);

            return $checkout->load('items.product');
        });
    }

    public function getDetail($checkoutId, $userId)
    {
        return Checkout::with('items.product')
            ->where('user_id', $userId)
            ->findOrFail($checkoutId);
    }

    public function cancelCheckout($checkoutId, $userId)
    {
        $checkout = Checkout::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($checkoutId);

        $checkout->update(['status' => 'canceled']);

        return $checkout;
    }
}
