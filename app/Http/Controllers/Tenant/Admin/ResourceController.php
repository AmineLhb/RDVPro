<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ResourceRequest;
use App\Models\Tenant\Resource;
use App\Models\Tenant\Service;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * List resources.
     */
    public function index()
    {
        $resources = Resource::with('services')->get();
        $services = Service::all();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return view('tenant.admin.resources.index', compact('resources', 'services', 'days'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return redirect()->route('tenant.resources.index', ['create' => 1]);
    }

    /**
     * Store resource.
     */
    public function store(ResourceRequest $request)
    {
        $data = $request->validated();
        
        // Structure default working hours if not provided
        if (empty($data['working_hours'])) {
            $workingHours = [];
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $workingHours[$day] = [
                    'open' => $request->input("open_{$day}", '09:00'),
                    'close' => $request->input("close_{$day}", '18:00'),
                    'active' => $request->has("active_{$day}"),
                ];
            }
            $data['working_hours'] = $workingHours;
        }

        $resource = Resource::create([
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'color' => $data['color'] ?? '#3d5a3d',
            'working_hours' => $data['working_hours'],
            'active' => $request->has('active'),
        ]);

        if (isset($data['services'])) {
            $resource->services()->sync($data['services']);
        }

        return redirect()->route('tenant.resources.index')->with('success', 'Personnel créé avec succès.');
    }

    /**
     * Show edit form.
     */
    public function edit(Resource $resource)
    {
        return redirect()->route('tenant.resources.index', ['edit' => $resource->id]);
    }

    /**
     * Update resource.
     */
    public function update(ResourceRequest $request, Resource $resource)
    {
        $data = $request->validated();

        if (empty($data['working_hours'])) {
            $workingHours = [];
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $workingHours[$day] = [
                    'open' => $request->input("open_{$day}", '09:00'),
                    'close' => $request->input("close_{$day}", '18:00'),
                    'active' => $request->has("active_{$day}"),
                ];
            }
            $data['working_hours'] = $workingHours;
        }

        $resource->update([
            'name' => $data['name'],
            'role' => $data['role'] ?? $resource->role,
            'color' => $data['color'] ?? $resource->color,
            'working_hours' => $data['working_hours'],
            'active' => $request->has('active'),
        ]);

        if (isset($data['services'])) {
            $resource->services()->sync($data['services']);
        }

        return redirect()->route('tenant.resources.index')->with('success', 'Personnel mis à jour.');
    }

    /**
     * Delete resource.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('tenant.resources.index')->with('success', 'Personnel supprimé.');
    }
}
