<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'preferred_channel',
        'notes',
        'last_visit_at',
    ];

    protected $casts = [
        'last_visit_at' => 'datetime',
    ];

    public function getPreferredChannelAttribute($value)
    {
        if (empty($value)) {
            return ['whatsapp'];
        }
        
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        
        if (str_contains($value, ',')) {
            return explode(',', $value);
        }
        
        return [$value];
    }

    public function setPreferredChannelAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['preferred_channel'] = json_encode(array_values(array_filter($value)));
        } else {
            $this->attributes['preferred_channel'] = json_encode([$value]);
        }
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'customer_label');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
