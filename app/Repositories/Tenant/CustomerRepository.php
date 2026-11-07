<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
{
    /**
     * Get all customers.
     */
    public function getAll(?string $search = ''): Collection
    {
        $search = $search ?? '';
        $query = Customer::with('labels')->orderBy('name', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Find customer by ID.
     */
    public function find(int $id): ?Customer
    {
        return Customer::with(['labels', 'appointments'])->find($id);
    }
}
