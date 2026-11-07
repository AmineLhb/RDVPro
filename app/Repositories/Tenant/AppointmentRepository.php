<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository
{
    /**
     * Get all appointments with relations.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Appointment::with(['customer', 'service', 'resource'])
            ->orderBy('starts_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('starts_at', Carbon::parse($filters['date']));
        }

        return $query->get();
    }

    /**
     * Get upcoming appointments.
     */
    public function getUpcoming(int $limit = 10): Collection
    {
        return Appointment::with(['customer', 'service', 'resource'])
            ->where('starts_at', '>=', Carbon::now())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->orderBy('starts_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get today's appointments.
     */
    public function getToday(): Collection
    {
        return Appointment::with(['customer', 'service', 'resource'])
            ->whereDate('starts_at', Carbon::today())
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('starts_at', 'asc')
            ->get();
    }

    /**
     * Find appointment by ID.
     */
    public function find(int $id): ?Appointment
    {
        return Appointment::with(['customer', 'service', 'resource'])->find($id);
    }
}
