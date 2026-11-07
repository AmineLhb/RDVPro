<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Show payment form and validation status.
     */
    public function showPay()
    {
        $tenant = tenant();
        return view('tenant.admin.subscription.pay', compact('tenant'));
    }

    /**
     * Store uploaded payment receipt.
     */
    public function storePay(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:1_month,3_months,1_year',
            'receipt' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // Max 5MB
        ], [
            'plan.required' => 'Veuillez sélectionner un plan d\'abonnement.',
            'plan.in' => 'Le plan sélectionné est invalide.',
            'receipt.required' => 'Veuillez téléverser votre reçu de paiement.',
            'receipt.file' => 'Le fichier du reçu de paiement est invalide.',
            'receipt.mimes' => 'Le reçu de paiement doit être au format PDF, JPEG, JPG ou PNG.',
            'receipt.max' => 'Le reçu de paiement ne doit pas dépasser 5 Mo.',
        ]);

        $tenant = tenant();

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = 'receipt_' . $tenant->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists in public/uploads/receipts
            $destinationPath = public_path('uploads/receipts');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Move file
            $file->move($destinationPath, $filename);
            
            // Clean up old file if it exists
            if ($tenant->receipt_path && file_exists(public_path($tenant->receipt_path))) {
                @unlink(public_path($tenant->receipt_path));
            }

            // Update tenant attributes in central DB
            $tenant->update([
                'selected_plan' => $request->plan,
                'receipt_path' => 'uploads/receipts/' . $filename,
                'receipt_status' => 'pending',
                'receipt_uploaded_at' => now(),
            ]);
        }

        return redirect()->route('tenant.subscription.pay')->with('success', 'Votre reçu a été envoyé avec succès. Le compte sera activé dès validation par l\'administrateur.');
    }
}
