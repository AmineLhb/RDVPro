<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB maximum size
                'mimes:jpg,jpeg,png,webp,pdf,docx', // Allowed file types
            ],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
            'record_id' => ['nullable', 'integer', 'exists:records,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Un fichier est requis.',
            'file.max' => 'La taille maximale du fichier est de 10 Mo.',
            'file.mimes' => 'Le fichier doit être au format : JPG, JPEG, PNG, WEBP, PDF ou DOCX.',
        ];
    }
}
