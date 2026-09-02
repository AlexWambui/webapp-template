<?php

namespace Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Enums\UserRoles;
use Modules\User\Enums\UserStatuses;

class UserRequest extends FormRequest
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
        $user = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', 'integer', 'in:' . implode(',', array_column(UserRoles::cases(), 'value'))],
            'status' => ['required', 'integer', 'in:' . implode(',', array_column(UserStatuses::cases(), 'value'))],
        ];

        if (!$user || $this->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'position.required_if' => 'Position is required for cashier accounts',
            'company_name.required_if' => 'Company name is required for supplier accounts',
            'payment_terms.required_if' => 'Payment terms are required for supplier accounts',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string role to integer if needed
        if ($this->has('role') && is_string($this->role)) {
            $this->merge([
                'role' => (int) $this->role,
            ]);
        }

        if ($this->isMethod('POST')) {
            $this->merge([
                'status' => $this->input('status', 1),
            ]);
        }
    }
}
