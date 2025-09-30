<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan request ini hanya bisa diakses user yang sudah login
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Items tidak boleh kosong.',
            'items.array'    => 'Items harus berupa array.',
            'items.min'      => 'Minimal 1 item harus dimasukkan.',
            'items.*.product_id.required' => 'Product ID wajib diisi.',
            'items.*.product_id.exists'   => 'Produk tidak ditemukan.',
            'items.*.quantity.required'   => 'Quantity wajib diisi.',
            'items.*.quantity.min'        => 'Quantity minimal 1.',
        ];
    }
}
