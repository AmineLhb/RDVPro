<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AppointmentStoreRequest;
use App\Http\Requests\Tenant\AppointmentUpdateRequest;
use App\Services\Tenant\AppointmentService;
use App\Services\Tenant\NotificationService;
use App\Repositories\Tenant\AppointmentRepository;
use App\Repositories\Tenant\CustomerRepository;
use App\Repositories\Tenant\ServiceRepository;
use App\Models\Tenant\Resource;
use App\Models\Tenant\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;
    protected NotificationService $notificationService;
    protected AppointmentRepository $appointmentRepo;
    protected CustomerRepository $customerRepo;
    protected ServiceRepository $serviceRepo;

    public function __construct(
        AppointmentService $appointmentService,
        NotificationService $notificationService,
        AppointmentRepository $appointmentRepo,
        CustomerRepository $customerRepo,
        ServiceRepository $serviceRepo
    ) {
        $this->appointmentService = $appointmentService;
        $this->notificationService = $notificationService;
        $this->appointmentRepo = $appointmentRepo;
        $this->customerRepo = $customerRepo;
        $this->serviceRepo = $serviceRepo;
    }

    /**
     * List appointments.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->get('format') === 'json') {
            $start = $request->get('start');
            $end = $request->get('end');

            $appointments = Appointment::with(['customer', 'service', 'resource'])
                ->where(function ($query) use ($start, $end) {
                    if ($start && $end) {
                        try {
                            $parsedStart = \Carbon\Carbon::parse($start)->toDateTimeString();
                            $parsedEnd = \Carbon\Carbon::parse($end)->toDateTimeString();
                            $query->whereBetween('starts_at', [$parsedStart, $parsedEnd]);
                        } catch (\Exception $e) {
                            $query->whereBetween('starts_at', [$start, $end]);
                        }
                    }
                })
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'title' => ($app->customer->name ?? 'Client') . ' - ' . ($app->service->name ?? 'Prestation'),
                        'start' => $app->starts_at->format('Y-m-d\TH:i:s'),
                        'end' => $app->ends_at->format('Y-m-d\TH:i:s'),
                        'backgroundColor' => $app->resource->color ?? '#3d5a3d',
                        'borderColor' => $app->resource->color ?? '#3d5a3d',
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'status' => $app->status,
                            'customer_id' => $app->customer_id,
                            'service_id' => $app->service_id,
                            'resource_id' => $app->resource_id,
                            'price' => $app->price,
                            'note' => $app->note,
                            'customer_name' => $app->customer->name ?? '',
                            'service_name' => $app->service->name ?? '',
                            'resource_name' => $app->resource->name ?? '',
                        ]
                    ];
                });
            return response()->json($appointments);
        }

        $customers = $this->customerRepo->getAll();
        $services = $this->serviceRepo->getAll(true);
        $resources = Resource::where('active', true)->get();

        return view('tenant.admin.appointments.index', compact('customers', 'services', 'resources'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return redirect()->route('tenant.appointments.index', ['create' => 1]);
    }

    /**
     * Store appointment.
     */
    public function store(AppointmentStoreRequest $request)
    {
        try {
            $appointment = $this->appointmentService->createAppointment($request->validated());
            
            // Dispatch simulated notification matching creation status
            $status = $appointment->status;
            $triggerMap = [
                'confirmed' => 'booking_confirmed',
                'completed' => 'booking_completed',
                'cancelled' => 'booking_cancelled',
                'no_show' => 'booking_no_show',
                'pending' => 'booking_pending',
            ];
            if (isset($triggerMap[$status])) {
                $this->notificationService->sendSimulatedNotification($appointment, $triggerMap[$status]);
            }

            return redirect()->route('tenant.appointments.index')->with('success', 'Rendez-vous créé avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }
    }

    /**
     * Show edit form.
     */
    public function edit(Appointment $appointment)
    {
        return redirect()->route('tenant.appointments.index', ['edit' => $appointment->id]);
    }

    /**
     * Update appointment.
     */
    public function update(AppointmentUpdateRequest $request, Appointment $appointment)
    {
        try {
            $oldStatus = $appointment->status;
            $newStatus = $request->input('status');

            $this->appointmentService->updateAppointment($appointment, $request->validated());

            if ($newStatus && $newStatus !== $oldStatus) {
                $triggerMap = [
                    'confirmed' => 'booking_confirmed',
                    'completed' => 'booking_completed',
                    'cancelled' => 'booking_cancelled',
                    'no_show' => 'booking_no_show',
                    'pending' => 'booking_pending',
                ];
                if (isset($triggerMap[$newStatus])) {
                    $this->notificationService->sendSimulatedNotification($appointment, $triggerMap[$newStatus]);
                }
            }

            return redirect()->route('tenant.appointments.index')->with('success', 'Rendez-vous modifié avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }
    }

    /**
     * Quick toggle / update status from list.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled,no_show',
        ]);

        try {
            $oldStatus = $appointment->status;
            $newStatus = $data['status'];

            $payload = $appointment->toArray();
            $payload['starts_at'] = $appointment->starts_at->toDateTimeString();
            $payload['ends_at'] = $appointment->ends_at->toDateTimeString();
            $payload['status'] = $newStatus;

            $this->appointmentService->updateAppointment($appointment, $payload);

            if ($newStatus !== $oldStatus) {
                $triggerMap = [
                    'confirmed' => 'booking_confirmed',
                    'completed' => 'booking_completed',
                    'cancelled' => 'booking_cancelled',
                    'no_show' => 'booking_no_show',
                    'pending' => 'booking_pending',
                ];
                if (isset($triggerMap[$newStatus])) {
                    $this->notificationService->sendSimulatedNotification($appointment, $triggerMap[$newStatus]);
                }
            }

            return redirect()->back()->with('success', 'Statut du rendez-vous mis à jour.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('tenant.appointments.index')->with('success', 'Rendez-vous supprimé.');
    }

    /**
     * Reschedule appointment via drag & drop.
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
            'resource_id' => 'sometimes|integer|exists:resources,id',
        ]);

        try {
            $startsAt = \Carbon\Carbon::parse($data['starts_at'])->toDateTimeString();
            $endsAt = \Carbon\Carbon::parse($data['ends_at'])->toDateTimeString();

            $payload = [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'resource_id' => $data['resource_id'] ?? $appointment->resource_id,
                'customer_id' => $appointment->customer_id,
                'service_id' => $appointment->service_id,
                'price' => $appointment->price,
                'status' => $appointment->status,
                'note' => $appointment->note,
            ];

            $this->appointmentService->updateAppointment($appointment, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous déplacé avec succès.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
