@extends('tenant.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Configuration Générale</h1>
        <p class="text-muted">Gérez les détails de votre entreprise et configurez vos règles de réservation.</p>
    </div>

    <!-- Form -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-6 md:p-8">
        <form action="{{ route('tenant.config.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Business Profile -->
            <div>
                <h3 class="text-lg font-semibold border-b border-line pb-2 mb-4 text-clay">Profil de l'établissement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold mb-2">Nom commercial</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $businessProfile->value['name'] ?? '') }}" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                    <div>
                        <label for="business_type" class="block text-sm font-semibold mb-2">Secteur d'activité</label>
                        <select name="business_type" id="business_type" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            <option value="salon" {{ (old('business_type', $businessProfile->value['business_type'] ?? '') === 'salon') ? 'selected' : '' }}>Salon de Coiffure / Barber</option>
                            <option value="clinic" {{ (old('business_type', $businessProfile->value['business_type'] ?? '') === 'clinic') ? 'selected' : '' }}>Dentiste / Clinique</option>
                            <option value="gym" {{ (old('business_type', $businessProfile->value['business_type'] ?? '') === 'gym') ? 'selected' : '' }}>Coach / Salle de Sport</option>
                            <option value="other" {{ (old('business_type', $businessProfile->value['business_type'] ?? '') === 'other') ? 'selected' : '' }}>Autre Secteur</option>
                        </select>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold mb-2">Téléphone de contact</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $businessProfile->value['phone'] ?? '') }}" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-semibold mb-2">Adresse physique</label>
                        <textarea name="address" id="address" rows="3" placeholder="Ex: Riad, Rabat" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">{{ old('address', $businessProfile->value['address'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Booking Rules -->
            <div>
                <h3 class="text-lg font-semibold border-b border-line pb-2 mb-4 text-clay">Règles de réservation</h3>
                <div class="space-y-4 p-4 border border-line rounded-xl bg-paper">
                    <!-- Rule 1 -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="require_confirmation" id="require_confirmation" value="1" 
                                {{ ($bookingRules->value['require_confirmation'] ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="require_confirmation" class="font-semibold text-ink">Validation requise</label>
                            <p class="text-muted text-xs">Les rendez-vous restent en attente jusqu'à validation manuelle.</p>
                        </div>
                    </div>

                    <!-- Rule 2 -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="allow_self_booking" id="allow_self_booking" value="1" 
                                {{ ($bookingRules->value['allow_self_booking'] ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="allow_self_booking" class="font-semibold text-ink">Réservation en ligne autorisée</label>
                            <p class="text-muted text-xs">Permettre aux clients de choisir eux-mêmes leur créneau en ligne.</p>
                        </div>
                    </div>

                    <!-- Rule 3 -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="deposit_on_vip" id="deposit_on_vip" value="1" 
                                {{ ($bookingRules->value['deposit_on_vip'] ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="deposit_on_vip" class="font-semibold text-ink">Acompte obligatoire pour clients VIP</label>
                            <p class="text-muted text-xs">Exige un paiement de dépôt même pour les clients étiquetés VIP.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-line flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm">
                    Enregistrer la configuration
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
