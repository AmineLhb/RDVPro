<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Customer;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'preferred_channel' => $data['preferred_channel'] ?? 'whatsapp',
                'notes' => $data['notes'] ?? null,
            ]);

            if (isset($data['labels'])) {
                $customer->labels()->sync($data['labels']);
            }

            return $customer;
        });
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            $customer->update([
                'name' => $data['name'] ?? $customer->name,
                'phone' => $data['phone'] ?? $customer->phone,
                'email' => $data['email'] ?? $customer->email,
                'preferred_channel' => $data['preferred_channel'] ?? $customer->preferred_channel,
                'notes' => $data['notes'] ?? $customer->notes,
            ]);

            if (isset($data['labels'])) {
                $customer->labels()->sync($data['labels']);
            }

            return $customer;
        });
    }
}
