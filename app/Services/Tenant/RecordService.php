<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Record;
use App\Models\Tenant\RecordFieldTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RecordService
{
    /**
     * Create a new record entry.
     */
    public function createRecord(array $data): Record
    {
        // Extract fields
        $customerId = $data['customer_id'];
        $appointmentId = $data['appointment_id'] ?? null;
        $staffId = Auth::guard('web')->id(); // Current logged-in tenant staff member
        $notes = $data['notes'] ?? null;

        // Custom fields (entry_data)
        // We filter data to match fields defined in the template
        $template = RecordFieldTemplate::first();
        $entryData = [];
        
        if ($template && is_array($template->field_definitions)) {
            foreach ($template->field_definitions as $field) {
                $fieldName = $field['name'];
                $entryData[$fieldName] = $data[$fieldName] ?? null;
            }
        }

        $record = Record::create([
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'staff_id' => $staffId,
            'entry_data' => $entryData,
            'notes' => $notes,
        ]);

        $userEmail = Auth::guard('web')->user()->email ?? 'system';
        Log::info("[Activity Log] User {$userEmail} (ID: {$staffId}) created record ID {$record->id} for customer ID {$customerId}");

        return $record;
    }

    /**
     * Delete a record entry.
     */
    public function deleteRecord(Record $record): bool
    {
        $recordId = $record->id;
        $customerId = $record->customer_id;
        $staffId = Auth::guard('web')->id() ?? 0;
        $userEmail = Auth::guard('web')->user()->email ?? 'system';

        $deleted = $record->delete();

        if ($deleted) {
            Log::info("[Activity Log] User {$userEmail} (ID: {$staffId}) deleted record ID {$recordId} for customer ID {$customerId}");
        }

        return $deleted;
    }
}
