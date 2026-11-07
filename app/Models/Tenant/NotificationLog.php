<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'channel',
        'trigger',
        'message_body',
        'status',
    ];
}
