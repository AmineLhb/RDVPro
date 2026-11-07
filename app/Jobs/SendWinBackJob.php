<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Appointment;
use App\Services\Tenant\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWinBackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        Tenant::all()->each(function ($tenant) use ($notificationService) {
            $tenant->run(function () use ($notificationService) {
                // Inactive = 60 days since last visit
                $cutoffDate = Carbon::now()->subDays(60);

                $inactiveCustomers = Customer::where('last_visit_at', '<=', $cutoffDate)
                    ->get();

                foreach ($inactiveCustomers as $customer) {
                    // Get latest appointment to render details correctly
                    $latestAppointment = $customer->appointments()->orderBy('starts_at', 'desc')->first();

                    if ($latestAppointment) {
                        $notificationService->sendSimulatedNotification($latestAppointment, 'win_back');
                        
                        // Update last_visit_at slightly to avoid double win_back spamming in consecutive runs
                        $customer->update([
                            'last_visit_at' => Carbon::now() // Reset timer or log it elsewhere
                        ]);
                    }
                }
            });
        });
    }
}
