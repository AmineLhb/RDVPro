<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class RecordFieldTemplate extends Model
{
    protected $table = 'record_field_templates';

    protected $fillable = [
        'business_type',
        'field_definitions',
    ];

    protected $casts = [
        'field_definitions' => 'array',
    ];
}
