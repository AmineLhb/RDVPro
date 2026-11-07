<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\TenantStoreRequest;
use App\Services\Central\TenantProvisioningService;
use App\Jobs\ProvisionTenantJob;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected TenantProvisioningService $provisioningService;

    public function __construct(TenantProvisioningService $provisioningService)
    {
        $this->provisioningService = $provisioningService;
    }

    /**
     * List all tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->latest()->get();
        return view('central.superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('central.superadmin.tenants.create');
    }

    /**
     * Store new tenant.
     */
    public function store(TenantStoreRequest $request)
    {
        $data = $request->validated();
        // Set default parent domain if not defined
        $data['parent_domain'] = str_ends_with($request->getHost(), '.test') || $request->getHost() === '127.0.0.1' || $request->getHost() === 'localhost' 
            ? 'rdvpro.test' 
            : 'rdvpro.ma';

        try {
            // Provision synchronously for instant local feedback if queue is sync
            if (config('queue.default') === 'sync') {
                $this->provisioningService->provision($data);
            } else {
                ProvisionTenantJob::dispatch($data);
            }

            return redirect()->route('central.tenants.index')->with('success', 'Tenant créé avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erreur lors du provisionnement: ' . $e->getMessage()]);
        }
    }

    /**
     * Show details of a specific tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('central.superadmin.tenants.show', compact('tenant'));
    }

    /**
     * Suspend/Reactivate tenant.
     */
    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        return redirect()->route('central.tenants.index')->with('success', "Le statut du tenant a été modifié : {$newStatus}.");
    }

    /**
     * Delete tenant.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('central.tenants.index')->with('success', 'Tenant supprimé avec succès.');
    }

    /**
     * Approve tenant payment receipt and extend subscription.
     */
    public function approveReceipt(Tenant $tenant)
    {
        if ($tenant->receipt_status !== 'pending' || !$tenant->selected_plan) {
            return back()->withErrors(['error' => 'Aucun reçu de paiement en attente pour ce client.']);
        }

        $now = now();
        $baseDate = $tenant->getSubscriptionExpirationDate();
        if (!$baseDate || $baseDate->isPast()) {
            $baseDate = $now;
        }

        $durationMonths = 1;
        if ($tenant->selected_plan === '3_months') {
            $durationMonths = 3;
        } elseif ($tenant->selected_plan === '1_year') {
            $durationMonths = 12;
        }

        $newExpiration = $baseDate->copy()->addMonths($durationMonths);

        $tenant->update([
            'subscription_ends_at' => $newExpiration,
            'receipt_status' => 'approved',
        ]);

        return redirect()->route('central.tenants.index')->with('success', "Le reçu de paiement pour {$tenant->name} a été validé. L'abonnement a été prolongé jusqu'au " . $newExpiration->format('d/m/Y') . ".");
    }

    /**
     * Reject tenant payment receipt.
     */
    public function rejectReceipt(Tenant $tenant)
    {
        if ($tenant->receipt_status !== 'pending') {
            return back()->withErrors(['error' => 'Aucun reçu de paiement en attente pour ce client.']);
        }

        $tenant->update([
            'receipt_status' => 'rejected',
        ]);

        return redirect()->route('central.tenants.index')->with('success', "Le reçu de paiement a été rejeté pour {$tenant->name}.");
    }

    /**
     * List all soft-deleted tenants.
     */
    public function trash()
    {
        $tenants = Tenant::onlyTrashed()->with('domains')->latest()->get();
        return view('central.superadmin.tenants.trash', compact('tenants'));
    }

    /**
     * Restore a soft-deleted tenant.
     */
    public function restore($tenantId)
    {
        $tenant = Tenant::onlyTrashed()->findOrFail($tenantId);
        $tenant->restore();

        return redirect()->route('central.tenants.trash')->with('success', "Le client {$tenant->name} a été restauré avec succès.");
    }

    /**
     * Permanently delete a tenant and all their database and domains.
     */
    public function forceDestroy($tenantId)
    {
        $tenant = Tenant::withTrashed()->findOrFail($tenantId);
        $tenant->forceDelete();

        return redirect()->route('central.tenants.index')->with('success', 'Le client et toutes ses données ont été définitivement supprimés.');
    }
}
