<?php

namespace App\Repositories\Central;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

class TenantRepository
{
    /**
     * Get all tenants with domains.
     */
    public function getAll(): Collection
    {
        return Tenant::with('domains')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find tenant by ID.
     */
    public function find(string $id): ?Tenant
    {
        return Tenant::with('domains')->find($id);
    }
}
