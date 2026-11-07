<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'duration_minutes',
        'price',
        'requires_deposit',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'requires_deposit' => 'boolean',
        'active' => 'boolean',
    ];

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'resource_service');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
