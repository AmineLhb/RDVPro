<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, SoftDeletes;

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'receipt_uploaded_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'business_type',
            'plan',
            'locale',
            'timezone',
            'status',
            'trial_ends_at',
            'subscription_ends_at',
            'receipt_path',
            'receipt_status',
            'selected_plan',
            'receipt_uploaded_at',
            'deleted_at',
        ];
    }

    /**
     * Checks if subscription is active.
     */
    public function isSubscriptionActive(): bool
    {
        $now = now();
        if ($this->trial_ends_at && $now->lt($this->trial_ends_at)) {
            return true;
        }
        if ($this->subscription_ends_at && $now->lt($this->subscription_ends_at)) {
            return true;
        }
        return false;
    }

    /**
     * Get the expiration date of current trial or subscription.
     */
    public function getSubscriptionExpirationDate()
    {
        $trial = $this->trial_ends_at;
        $sub = $this->subscription_ends_at;
        if ($trial && $sub) {
            return $trial->gt($sub) ? $trial : $sub;
        }
        return $trial ?: $sub;
    }

    /**
     * Get number of days remaining on the subscription/trial.
     */
    public function daysLeftForSubscription(): int
    {
        $exp = $this->getSubscriptionExpirationDate();
        if (!$exp) return 0;
        if (now()->gt($exp)) return 0;
        return (int) ceil(now()->diffInHours($exp, false) / 24);
    }

    /**
     * Check if warning banner should be displayed.
     */
    public function shouldShowSubscriptionWarning(): bool
    {
        if (!$this->isSubscriptionActive()) {
            return false;
        }
        $days = $this->daysLeftForSubscription();
        return $days >= 0 && $days <= 10;
    }
}
