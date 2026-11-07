<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Setting;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Show config page.
     */
    public function index()
    {
        $businessProfile = Setting::firstOrCreate(['key' => 'business_profile'], [
            'value' => ['name' => 'RDVPro Client', 'business_type' => 'salon', 'phone' => '', 'address' => '']
        ]);

        $bookingRules = Setting::firstOrCreate(['key' => 'booking_rules'], [
            'value' => ['require_confirmation' => true, 'allow_self_booking' => true, 'deposit_on_vip' => false]
        ]);

        return view('tenant.admin.config.index', compact('businessProfile', 'bookingRules'));
    }

    /**
     * Save config details.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|in:salon,clinic,gym,other',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'require_confirmation' => 'nullable|boolean',
            'allow_self_booking' => 'nullable|boolean',
            'deposit_on_vip' => 'nullable|boolean',
        ]);

        $profile = Setting::where('key', 'business_profile')->first();
        $profile->update([
            'value' => [
                'name' => $request->input('name'),
                'business_type' => $request->input('business_type'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
            ]
        ]);

        $rules = Setting::where('key', 'booking_rules')->first();
        $rules->update([
            'value' => [
                'require_confirmation' => $request->has('require_confirmation'),
                'allow_self_booking' => $request->has('allow_self_booking'),
                'deposit_on_vip' => $request->has('deposit_on_vip'),
            ]
        ]);

        return redirect()->route('tenant.config.index')->with('success', 'Configuration mise à jour avec succès.');
    }
}
