<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class DomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
        ];
    }
}
