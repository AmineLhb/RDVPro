<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Record extends Model
{
    protected $table = 'records';

    protected $fillable = [
        'customer_id',
        'appointment_id',
        'staff_id',
        'entry_data',
        'notes',
    ];

    protected $casts = [
        'entry_data' => 'array',
    ];

    /**
     * Get the customer this record belongs to.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the appointment associated with this record.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the staff member who created this record.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the attachments for this record.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
