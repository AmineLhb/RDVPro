<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'requires_deposit' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'resources' => ['nullable', 'array'],
            'resources.*' => ['integer', 'exists:resources,id'],
        ];
    }
}
