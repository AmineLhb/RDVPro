<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Display the export page.
     */
    public function index()
    {
        return view('central.superadmin.exports.index');
    }

    /**
     * Export payment receipts uploaded in date range as a ZIP archive.
     */
    public function exportZip(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début est invalide.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin est invalide.',
            'end_date.after_or_equal' => 'La date de fin doit être supérieure ou égale à la date de début.',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query tenants having receipts uploaded within the period
        $tenants = Tenant::whereNotNull('receipt_path')
            ->whereNotNull('receipt_uploaded_at')
            ->whereBetween('receipt_uploaded_at', [$startDate, $endDate])
            ->get();

        if ($tenants->isEmpty()) {
            return back()->withErrors(['error' => 'Aucun reçu de paiement téléversé trouvé dans cet intervalle de dates.']);
        }

        $zipFileName = 'recus_' . $startDate->format('Y-m-d') . '_au_' . $endDate->format('Y-m-d') . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // Ensure storage directory exists
        $dir = dirname($zipFilePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $addedFiles = 0;
            foreach ($tenants as $tenant) {
                $filePath = public_path($tenant->receipt_path);
                if (file_exists($filePath)) {
                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                    $dateStr = $tenant->receipt_uploaded_at ? $tenant->receipt_uploaded_at->format('Y-m-d') : 'inconnue';
                    // Descriptive filename inside zip, e.g. "bahja_3_months_2026-06-13.pdf"
                    $fileNameInZip = $tenant->id . '_' . ($tenant->selected_plan ?? 'inconnu') . '_' . $dateStr . '.' . $ext;
                    $zip->addFile($filePath, $fileNameInZip);
                    $addedFiles++;
                }
            }
            $zip->close();
            
            if ($addedFiles === 0) {
                @unlink($zipFilePath);
                return back()->withErrors(['error' => 'Aucun fichier physique de reçu n\'a été trouvé sur le serveur pour cette période.']);
            }
        } else {
            return back()->withErrors(['error' => 'Erreur technique lors de la création de l\'archive ZIP.']);
        }

        if (!file_exists($zipFilePath)) {
            return back()->withErrors(['error' => 'Le fichier ZIP n\'a pas pu être localisé sur le serveur.']);
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
