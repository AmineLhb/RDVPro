<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Appointment;
use App\Models\Tenant\Service;
use App\Models\Tenant\Resource;
use App\Models\Tenant\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get dashboard / report metrics.
     */
    public function getOverviewMetrics(): array
    {
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::today()->subDays(30);

        // 1. Today's bookings count
        $todaysBookings = Appointment::whereDate('starts_at', $today)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        // 2. Today's revenue
        $todaysRevenue = Appointment::whereDate('starts_at', $today)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('price');

        // 3. Total appointments
        $totalBookings = Appointment::count();

        // 4. Total revenue (all time)
        $totalRevenue = Appointment::whereIn('status', ['confirmed', 'completed'])
            ->sum('price');

        // 5. No-show rate (last 30 days)
        $totalEnded = Appointment::where('starts_at', '>=', $thirtyDaysAgo)
            ->whereIn('status', ['completed', 'no_show'])
            ->count();
        $noShows = Appointment::where('starts_at', '>=', $thirtyDaysAgo)
            ->where('status', 'no_show')
            ->count();

        $noShowRate = $totalEnded > 0 ? round(($noShows / $totalEnded) * 100) : 0;

        // 6. Total customers
        $totalCustomers = Customer::count();

        // 7. New customers in the last 30 days
        $newCustomers30Days = Customer::where('created_at', '>=', $thirtyDaysAgo)->count();

        return [
            'todays_bookings' => $todaysBookings,
            'todays_revenue' => $todaysRevenue,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'no_show_rate' => $noShowRate,
            'total_customers' => $totalCustomers,
            'new_customers_30days' => $newCustomers30Days,
        ];
    }

    /**
     * Get revenue trend for the last 6 months.
     */
    public function getRevenueTrend(): array
    {
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthName = $monthStart->translatedFormat('M'); // Jan, Feb...

            $revenue = Appointment::whereBetween('starts_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('price');

            $trend[] = [
                'month' => $monthName,
                'revenue' => (float) $revenue,
            ];
        }

        return $trend;
    }

    /**
     * Get status breakdown.
     */
    public function getStatusBreakdown(): array
    {
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $counts = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $breakdown = [];
        $total = array_sum($counts);

        foreach ($statuses as $status) {
            $count = $counts[$status] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $breakdown[] = [
                'status' => $status,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return $breakdown;
    }

    /**
     * Get top services.
     */
    public function getTopServices(int $limit = 4): array
    {
        return Appointment::select('service_id', DB::raw('count(*) as count'), DB::raw('sum(price) as total_revenue'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->service->name ?? 'Service inconnu',
                    'count' => $item->count,
                    'revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Get staff performance.
     */
    public function getStaffPerformance(): array
    {
        return Appointment::select('resource_id', DB::raw('count(*) as count'), DB::raw('sum(price) as total_revenue'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('resource_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->resource->name ?? 'Staff inconnu',
                    'role' => $item->resource->role ?? '',
                    'count' => $item->count,
                    'revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }
}
