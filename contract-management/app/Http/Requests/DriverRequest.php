<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canEditDrivers();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'license_expiry' => ['nullable', 'date'],
            'status' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Driver name is required.',
            'phone.max' => 'Phone number is too long.',
            'license_expiry.date' => 'Please provide a valid date.',
        ];
    }
}
