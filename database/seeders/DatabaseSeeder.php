<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Services\Central\TenantProvisioningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed SuperAdmin
        SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'admin@rdvpro.ma',
            'password' => Hash::make('password'),
        ]);

        // 2. Seed Demo Tenant
        $provisioningService = app(TenantProvisioningService::class);
        $provisioningService->provision([
            'name' => 'Salon Bahja',
            'subdomain' => 'bahja',
            'business_type' => 'salon',
            'plan' => 'pro',
            'admin_name' => 'Karim Bahja',
            'admin_email' => 'karim@bahja.ma',
            'admin_password' => 'password',
            'admin_phone' => '+212 661 22 33 44',
            'parent_domain' => 'rdvpro.test', // default local test domain
        ]);
    }
}
