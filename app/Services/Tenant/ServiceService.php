<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Service;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    public function createService(array $data): Service
    {
        return DB::transaction(function () use ($data) {
            $service = Service::create([
                'name' => $data['name'],
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'],
                'requires_deposit' => $data['requires_deposit'] ?? false,
                'active' => $data['active'] ?? true,
            ]);

            if (isset($data['resources'])) {
                $service->resources()->sync($data['resources']);
            }

            return $service;
        });
    }

    public function updateService(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {
            $service->update([
                'name' => $data['name'] ?? $service->name,
                'duration_minutes' => $data['duration_minutes'] ?? $service->duration_minutes,
                'price' => $data['price'] ?? $service->price,
                'requires_deposit' => $data['requires_deposit'] ?? $service->requires_deposit,
                'active' => $data['active'] ?? $service->active,
            ]);

            if (isset($data['resources'])) {
                $service->resources()->sync($data['resources']);
            }

            return $service;
        });
    }
}
