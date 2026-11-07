<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\RecordFieldTemplate;
use Illuminate\Foundation\Http\FormRequest;

class RecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
            'notes' => ['nullable', 'string'],
        ];

        // Dynamic validation rules based on the active template
        $template = RecordFieldTemplate::first();
        if ($template && is_array($template->field_definitions)) {
            foreach ($template->field_definitions as $field) {
                $fieldName = $field['name'];
                $fieldRules = [];

                if (!empty($field['required'])) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                $fieldRules[] = 'string';

                $rules[$fieldName] = $fieldRules;
            }
        }

        return $rules;
    }
}
