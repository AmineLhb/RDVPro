<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository
{
    /**
     * Get all services.
     */
    public function getAll(bool $onlyActive = false): Collection
    {
        $query = Service::with('resources')->orderBy('name', 'asc');

        if ($onlyActive) {
            $query->where('active', true);
        }

        return $query->get();
    }

    /**
     * Find service by ID.
     */
    public function find(int $id): ?Service
    {
        return Service::with('resources')->find($id);
    }
}
