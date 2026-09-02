<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\DeliveryStatus;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_channel' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_method' => 'required|in:shop,delivery',
            'location' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'delivery_cost' => 'required|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.method' => 'required|string|in:mpesa,cash',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.id' => 'required|exists:products,id',
            'cart_items.*.price' => 'required|numeric|min:0',
        ];
    }
}
