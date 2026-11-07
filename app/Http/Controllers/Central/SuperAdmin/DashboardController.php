<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();
        $tenants = Tenant::with('domains')->latest()->limit(5)->get();

        return view('central.superadmin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'suspendedTenants',
            'tenants'
        ));
    }
}
