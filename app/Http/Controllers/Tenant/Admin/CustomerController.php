<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CustomerRequest;
use App\Services\Tenant\CustomerService;
use App\Repositories\Tenant\CustomerRepository;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Label;
use App\Models\Tenant\RecordFieldTemplate;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;
    protected CustomerRepository $customerRepo;

    public function __construct(CustomerService $customerService, CustomerRepository $customerRepo)
    {
        $this->customerService = $customerService;
        $this->customerRepo = $customerRepo;
    }

    /**
     * List customers.
     */
    public function index(Request $request)
    {
        $search = $request->get('search') ?? '';
        $customers = $this->customerRepo->getAll($search);
        $labels = Label::all();
        return view('tenant.admin.customers.index', compact('customers', 'search', 'labels'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return redirect()->route('tenant.customers.index', ['create' => 1]);
    }

    /**
     * Store customer.
     */
    public function store(CustomerRequest $request)
    {
        $this->customerService->createCustomer($request->validated());
        return redirect()->route('tenant.customers.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * Show edit form.
     */
    public function edit(Customer $customer)
    {
        return redirect()->route('tenant.customers.index', ['edit' => $customer->id]);
    }

    /**
     * Update customer.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->customerService->updateCustomer($customer, $request->validated());
        return redirect()->route('tenant.customers.index')->with('success', 'Client mis à jour.');
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('tenant.customers.index')->with('success', 'Client supprimé.');
    }

    /**
     * Show customer profile.
     */
    public function show(Customer $customer, Request $request)
    {
        // 1. Fetch appointments with filters
        $appointmentsQuery = $customer->appointments()->with(['service', 'resource']);

        if ($request->filled('start_date')) {
            $appointmentsQuery->whereDate('starts_at', '>=', $request->get('start_date'));
        }
        if ($request->filled('end_date')) {
            $appointmentsQuery->whereDate('starts_at', '<=', $request->get('end_date'));
        }
        if ($request->filled('status')) {
            $appointmentsQuery->where('status', $request->get('status'));
        }
        if ($request->filled('service_id')) {
            $appointmentsQuery->where('service_id', $request->get('service_id'));
        }
        if ($request->filled('resource_id')) {
            $appointmentsQuery->where('resource_id', $request->get('resource_id'));
        }
        if ($request->filled('search_note')) {
            $appointmentsQuery->where('note', 'like', "%{$request->get('search_note')}%");
        }

        $sort = $request->get('sort') === 'asc' ? 'asc' : 'desc';
        $appointmentsQuery->orderBy('starts_at', $sort);

        $appointments = $appointmentsQuery->paginate(10, ['*'], 'history_page')->withQueryString();

        // Format all customer appointments for Alpine.js live filtering
        $allAppointmentsArray = $customer->appointments()->with(['service', 'resource'])->get()->map(function($app) {
            return [
                'id' => $app->id,
                'starts_at' => $app->starts_at->format('Y-m-d H:i'),
                'starts_at_formatted' => $app->starts_at->translatedFormat('d M Y'),
                'starts_at_time' => $app->starts_at->format('H:i'),
                'starts_at_raw' => $app->starts_at->toIso8601String(),
                'service_name' => $app->service->name,
                'service_id' => $app->service_id,
                'resource_name' => $app->resource->name,
                'resource_id' => $app->resource_id,
                'price' => (float) $app->price,
                'status' => $app->status,
                'note' => $app->note ?? '',
            ];
        });

        // 2. Fetch records timeline & templates
        $recordRepo = app(\App\Repositories\Tenant\RecordRepository::class);
        $records = $recordRepo->getForCustomer($customer->id, 15);

        // Fetch general/standalone attachments (not linked to any record entry)
        $generalAttachments = $customer->attachments()
            ->whereNull('record_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all of the customer's attachments (for preview navigation)
        $allAttachments = $customer->attachments()
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch lists for appointment filters
        $services = \App\Models\Tenant\Service::all();
        $resources = \App\Models\Tenant\Resource::all();

        // Fetch active record field template
        $template = RecordFieldTemplate::first();
        if (!$template) {
            $template = RecordFieldTemplate::create([
                'business_type' => tenant('business_type') ?? 'general',
                'field_definitions' => [
                    ['name' => 'subject', 'label' => 'Sujet / Motif', 'type' => 'text', 'required' => true],
                    ['name' => 'details', 'label' => 'Détails & Compte-rendu', 'type' => 'long_text', 'required' => false],
                ],
            ]);
        }

        // Get past and upcoming appointments for linking records
        $linkableAppointments = $customer->appointments()
            ->with(['service', 'resource'])
            ->orderBy('starts_at', 'desc')
            ->get();

        return view('tenant.admin.customers.show', compact(
            'customer',
            'appointments',
            'allAppointmentsArray',
            'records',
            'generalAttachments',
            'allAttachments',
            'services',
            'resources',
            'template',
            'linkableAppointments'
        ));
    }
}
