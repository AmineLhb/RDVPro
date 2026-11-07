<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ServiceRequest;
use App\Services\Tenant\ServiceService;
use App\Repositories\Tenant\ServiceRepository;
use App\Models\Tenant\Service;
use App\Models\Tenant\Resource;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected ServiceService $serviceService;
    protected ServiceRepository $serviceRepo;

    public function __construct(ServiceService $serviceService, ServiceRepository $serviceRepo)
    {
        $this->serviceService = $serviceService;
        $this->serviceRepo = $serviceRepo;
    }

    public function index()
    {
        $services = Service::with('resources')->get();
        $resources = Resource::all();
        return view('tenant.admin.services.index', compact('services', 'resources'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return redirect()->route('tenant.services.index', ['create' => 1]);
    }

    /**
     * Store service.
     */
    public function store(ServiceRequest $request)
    {
        $this->serviceService->createService($request->validated());
        return redirect()->route('tenant.services.index')->with('success', 'Prestation créée avec succès.');
    }

    /**
     * Show edit form.
     */
    public function edit(Service $service)
    {
        return redirect()->route('tenant.services.index', ['edit' => $service->id]);
    }

    /**
     * Update service.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $this->serviceService->updateService($service, $request->validated());
        return redirect()->route('tenant.services.index')->with('success', 'Prestation mise à jour.');
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('tenant.services.index')->with('success', 'Prestation supprimée.');
    }
}
