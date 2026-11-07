<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Record;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RecordRepository
{
    /**
     * Get records for a customer.
     */
    public function getForCustomer(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        return Record::with(['staff', 'appointment.resource', 'appointment.service', 'attachments'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find a record by ID.
     */
    public function find(int $id): ?Record
    {
        return Record::with(['staff', 'appointment', 'attachments'])->find($id);
    }
}
