<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class TenantStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'alpha_dash', 'max:50'],
            'business_type' => ['required', 'string', 'in:salon,clinic,gym,other'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8'],
            'admin_phone' => ['nullable', 'string', 'max:50'],
            'custom_domain' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'in:starter,pro,business'],
        ];
    }
}
