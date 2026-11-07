<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Appointment;
use App\Models\Tenant\MessageTemplate;
use App\Models\Tenant\NotificationLog;
use App\Models\Tenant\Setting;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send simulated notification for an appointment trigger.
     */
    public function sendSimulatedNotification(Appointment $appointment, string $trigger): void
    {
        // 1. Get customer
        $customer = $appointment->customer;
        if (!$customer) {
            return;
        }

        // 2. Determine preferred channels
        $channels = $customer->preferred_channel;
        if (empty($channels)) {
            $channels = ['whatsapp'];
        }

        // 3. Send notifications for each preferred channel
        foreach ($channels as $channel) {
            $template = MessageTemplate::where('trigger', $trigger)
                ->where('channel', $channel)
                ->where('active', true)
                ->first();

            if (!$template) {
                // Fallback to email if no template found
                $template = MessageTemplate::where('trigger', $trigger)
                    ->where('active', true)
                    ->first();
            }

            if (!$template) {
                continue;
            }

            // 4. Render template
            $renderedBody = $this->renderTemplate($template->body, $appointment);
            $subject = $template->subject ? $this->renderTemplate($template->subject, $appointment) : null;

            // 5. Save to database log (the simulated dispatch!)
            NotificationLog::create([
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'channel' => $template->channel,
                'trigger' => $trigger,
                'message_body' => $subject ? "[Sujet: {$subject}] \n\n {$renderedBody}" : $renderedBody,
                'status' => 'Envoyé (Simulé)',
            ]);

            Log::info("Simulated Notification sent via {$template->channel} to {$customer->name} ({$customer->phone}) for trigger {$trigger}");
        }
    }

    /**
     * Render a string template with appointment details.
     */
    public function renderTemplate(string $text, Appointment $appointment): string
    {
        $customer = $appointment->customer;
        $service = $appointment->service;
        $resource = $appointment->resource;

        // Fetch business name from settings
        $businessProfile = Setting::where('key', 'business_profile')->first();
        $businessName = $businessProfile ? ($businessProfile->value['name'] ?? 'RDVPro Client') : 'RDVPro Client';

        $replacements = [
            '{name}' => $customer->name ?? '',
            '{business}' => $businessName,
            '{date}' => $appointment->starts_at ? $appointment->starts_at->format('d/m/Y') : '',
            '{time}' => $appointment->starts_at ? $appointment->starts_at->format('H:i') : '',
            '{service}' => $service->name ?? '',
            '{staff}' => $resource->name ?? '',
            '{price}' => ($appointment->price ?? $service->price ?? '0') . ' MAD',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
