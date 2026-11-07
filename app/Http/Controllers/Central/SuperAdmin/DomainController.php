<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\DomainRequest;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * List all domains.
     */
    public function index()
    {
        $domains = Domain::with('tenant')->latest()->get();
        $tenants = Tenant::all();
        return view('central.superadmin.domains.index', compact('domains', 'tenants'));
    }

    /**
     * Store new custom domain.
     */
    public function store(DomainRequest $request)
    {
        Domain::create($request->validated());
        return redirect()->route('central.domains.index')->with('success', 'Domaine lié avec succès.');
    }

    /**
     * Delete domain mapping.
     */
    public function destroy(Domain $domain)
    {
        // Don't delete the primary subdomain if it's the only one
        $tenant = $domain->tenant;
        if ($tenant->domains()->count() <= 1) {
            return back()->withErrors(['error' => 'Impossible de supprimer le seul domaine associé à ce tenant.']);
        }

        $domain->delete();
        return redirect()->route('central.domains.index')->with('success', 'Domaine supprimé avec succès.');
    }
}
