<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Appointment;
use App\Models\Tenant\Resource;
use App\Models\Tenant\Customer;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Create a new appointment after validation.
     */
    public function createAppointment(array $data): Appointment
    {
        $this->validateAppointment($data);

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);

        $appointment = Appointment::create([
            'customer_id' => $data['customer_id'],
            'service_id' => $data['service_id'],
            'resource_id' => $data['resource_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $data['status'] ?? 'pending',
            'price' => $data['price'],
            'note' => $data['note'] ?? null,
        ]);

        return $appointment;
    }

    /**
     * Update an appointment after validation.
     */
    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $this->validateAppointment($data, $appointment->id);

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);

        // Check if status is transitioning to completed
        $oldStatus = $appointment->status;
        $newStatus = $data['status'] ?? $appointment->status;

        $appointment->update([
            'customer_id' => $data['customer_id'] ?? $appointment->customer_id,
            'service_id' => $data['service_id'] ?? $appointment->service_id,
            'resource_id' => $data['resource_id'] ?? $appointment->resource_id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $newStatus,
            'price' => $data['price'] ?? $appointment->price,
            'note' => $data['note'] ?? $appointment->note,
        ]);

        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $appointment->customer->update([
                'last_visit_at' => Carbon::now()
            ]);
        }

        return $appointment;
    }

    /**
     * Validate working hours and double booking.
     */
    public function validateAppointment(array $data, ?int $excludeAppointmentId = null): void
    {
        $resourceId = $data['resource_id'];
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);

        if ($startsAt->gte($endsAt)) {
            throw ValidationException::withMessages([
                'starts_at' => 'L\'heure de début doit être antérieure à l\'heure de fin.'
            ]);
        }

        // 1. Fetch resource and check if active
        $resource = Resource::findOrFail($resourceId);
        if (!$resource->active) {
            throw ValidationException::withMessages([
                'resource_id' => 'Le membre du personnel sélectionné n\'est pas actif.'
            ]);
        }

        // 2. Validate against working hours
        $dayOfWeek = $startsAt->format('l'); // e.g. "Monday"
        $workingHours = $resource->working_hours;

        if (!isset($workingHours[$dayOfWeek]) || !($workingHours[$dayOfWeek]['active'] ?? false)) {
            throw ValidationException::withMessages([
                'starts_at' => "Le personnel n'est pas disponible le {$dayOfWeek}."
            ]);
        }

        $openTimeStr = $workingHours[$dayOfWeek]['open'];
        $closeTimeStr = $workingHours[$dayOfWeek]['close'];

        $openTime = Carbon::parse($startsAt->format('Y-m-d') . ' ' . $openTimeStr);
        $closeTime = Carbon::parse($startsAt->format('Y-m-d') . ' ' . $closeTimeStr);

        if ($startsAt->lt($openTime) || $endsAt->gt($closeTime)) {
            throw ValidationException::withMessages([
                'starts_at' => "L'horaire demandé doit être compris dans les heures de travail de {$resource->name} ({$openTimeStr} - {$closeTimeStr})."
            ]);
        }

        // 3. Prevent double booking
        $overlapping = Appointment::where('resource_id', $resourceId)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->where('starts_at', '<', $endsAt)
                      ->where('ends_at', '>', $startsAt);
            });

        if ($excludeAppointmentId) {
            $overlapping->where('id', '!=', $excludeAppointmentId);
        }

        if ($overlapping->exists()) {
            throw ValidationException::withMessages([
                'starts_at' => 'Le personnel sélectionné a déjà un rendez-vous planifié sur ce créneau.'
            ]);
        }
    }
}
