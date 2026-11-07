<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Tenant\Appointment;
use App\Services\Tenant\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        Tenant::all()->each(function ($tenant) use ($notificationService) {
            $tenant->run(function () use ($notificationService) {
                $now = Carbon::now();
                $target24hStart = $now->copy()->addHours(23);
                $target24hEnd = $now->copy()->addHours(25);

                // Fetch confirmed appointments in the 24h window
                $appointments = Appointment::whereNull('reminder_sent_at')
                    ->whereBetween('starts_at', [$target24hStart, $target24hEnd])
                    ->where('status', 'confirmed')
                    ->get();

                foreach ($appointments as $appointment) {
                    $notificationService->sendSimulatedNotification($appointment, 'reminder_24h');
                    $appointment->update([
                        'reminder_sent_at' => Carbon::now()
                    ]);
                }
            });
        });
    }
}
