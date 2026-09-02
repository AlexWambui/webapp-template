<?php

namespace Modules\Delivery\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryAreaRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('delivery_areas', 'name')
                    ->where('delivery_location_id', $this->delivery_location_id)
                    ->ignore($this->route('delivery_area'))
            ],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'estimated_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'delivery_location_id' => ['required', 'exists:delivery_locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This area name already exists for this delivery location.',
            'slug.unique' => 'This slug is already in use.',
            'price.min' => 'Price cannot be negative.',
            'estimated_days.min' => 'Estimated days must be at least 1.',
            'estimated_days.max' => 'Estimated days cannot exceed 30.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('sort_order')) {
            $this->merge(['sort_order' => 0]);
        }

        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
