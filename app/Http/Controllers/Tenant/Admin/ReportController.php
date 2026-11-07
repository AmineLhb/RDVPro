<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Services\Tenant\ReportService;
use App\Exports\AppointmentsExport;
use App\Models\Tenant\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Render the reports page.
     */
    public function index()
    {
        $metrics = $this->reportService->getOverviewMetrics();
        $revenueTrend = $this->reportService->getRevenueTrend();
        $statusBreakdown = $this->reportService->getStatusBreakdown();
        $topServices = $this->reportService->getTopServices();
        $staffPerformance = $this->reportService->getStaffPerformance();

        return view('tenant.admin.reports.index', compact(
            'metrics',
            'revenueTrend',
            'statusBreakdown',
            'topServices',
            'staffPerformance'
        ));
    }

    /**
     * Export PDF report.
     */
    public function exportPdf()
    {
        $appointments = Appointment::with(['customer', 'service', 'resource'])
            ->orderBy('starts_at', 'asc')
            ->get();
            
        $metrics = $this->reportService->getOverviewMetrics();
        
        $pdf = Pdf::loadView('tenant.admin.reports.pdf', compact('appointments', 'metrics'));
        return $pdf->download('rapport_rendez_vous.pdf');
    }

    /**
     * Export Excel report.
     */
    public function exportExcel()
    {
        return Excel::download(new AppointmentsExport, 'rapport_rendez_vous.xlsx');
    }
}
