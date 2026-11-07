<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Services\Tenant\ReportService;
use App\Repositories\Tenant\AppointmentRepository;
use App\Models\Tenant\NotificationLog;

class DashboardController extends Controller
{
    protected ReportService $reportService;
    protected AppointmentRepository $appointmentRepo;

    public function __construct(ReportService $reportService, AppointmentRepository $appointmentRepo)
    {
        $this->reportService = $reportService;
        $this->appointmentRepo = $appointmentRepo;
    }

    public function index()
    {
        $metrics = $this->reportService->getOverviewMetrics();
        $upcomingAppointments = $this->appointmentRepo->getUpcoming(5);
        $logs = NotificationLog::latest()->limit(5)->get();

        return view('tenant.admin.dashboard', compact('metrics', 'upcomingAppointments', 'logs'));
    }

    public function home()
    {
        return view('tenant.admin.home');
    }
}
