<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Tenant\Appointment;

class AppointmentsExport implements FromCollection, WithHeadings
{
    /**
     * Return appointments collection.
     */
    public function collection()
    {
        return Appointment::with(['customer', 'service', 'resource'])
            ->get()
            ->map(function ($app) {
                return [
                    'ID' => $app->id,
                    'Client' => $app->customer->name ?? '',
                    'Prestation' => $app->service->name ?? '',
                    'Personnel' => $app->resource->name ?? '',
                    'Date' => $app->starts_at ? $app->starts_at->format('Y-m-d H:i') : '',
                    'Statut' => $app->status,
                    'Prix' => $app->price . ' MAD',
                ];
            });
    }

    /**
     * Table headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Client',
            'Prestation',
            'Personnel',
            'Date & Heure',
            'Statut',
            'Prix',
        ];
    }
}
