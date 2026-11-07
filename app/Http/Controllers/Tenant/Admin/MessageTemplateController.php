<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\MessageTemplateRequest;
use App\Models\Tenant\MessageTemplate;
use App\Models\Tenant\Appointment;
use App\Services\Tenant\NotificationService;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List and edit templates.
     */
    public function index()
    {
        $templates = MessageTemplate::all();
        
        // Find a representative appointment to use for preview, or create a mock one
        $mockAppointment = Appointment::first() ?? $this->getMockAppointmentForPreview();
        
        return view('tenant.admin.templates.index', compact('templates', 'mockAppointment'));
    }

    /**
     * Update template.
     */
    public function update(MessageTemplateRequest $request, MessageTemplate $template)
    {
        $template->update($request->validated());
        return redirect()->route('tenant.templates.index')->with('success', 'Modèle mis à jour avec succès.');
    }

    /**
     * AJAX rendering preview helper.
     */
    public function previewRender(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $mockAppointment = Appointment::first() ?? $this->getMockAppointmentForPreview();
        $rendered = $this->notificationService->renderTemplate($request->input('body'), $mockAppointment);

        return response()->json(['rendered' => $rendered]);
    }

    /**
     * Helper to instantiate a temporary mock appointment with relations if none exist.
     */
    private function getMockAppointmentForPreview(): Appointment
    {
        $appointment = new Appointment();
        $appointment->starts_at = now()->addDays(1)->setHour(10)->setMinute(0);
        $appointment->price = 100.00;

        $customer = new \App\Models\Tenant\Customer();
        $customer->name = 'Youssef Alami';
        $customer->phone = '+212 661 223 344';
        $appointment->setRelation('customer', $customer);

        $service = new \App\Models\Tenant\Service();
        $service->name = 'Coupe + Couleur';
        $appointment->setRelation('service', $service);

        $resource = new \App\Models\Tenant\Resource();
        $resource->name = 'Karim';
        $appointment->setRelation('resource', $resource);

        return $appointment;
    }
}
