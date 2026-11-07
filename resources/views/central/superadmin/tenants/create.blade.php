@extends('central.layout')

@section('content')
<div class="space-y-8 max-w-3xl">
    <!-- Header -->
    <div>
        <a href="{{ route('central.tenants.index') }}" class="text-sm text-muted hover:text-ink font-semibold flex items-center mb-2 font-outfit">
            ← Retour aux clients
        </a>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Créer un client</h1>
        <p class="text-muted text-sm font-outfit mt-1">Créez un nouveau client (tenant), son sous-domaine et son administrateur.</p>
    </div>

    <!-- Form -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-6 md:p-8">
        <form action="{{ route('central.tenants.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Business Profile -->
            <div>
                <h3 class="text-lg font-semibold border-b border-line pb-2 mb-4 text-clay">Profil de l'entreprise</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold mb-2">Nom de l'entreprise</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Ex: Salon Bahja, Cabinet Alami" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                    <div>
                        <label for="business_type" class="block text-sm font-semibold mb-2">Type d'activité</label>
                        <select name="business_type" id="business_type" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            <option value="salon" {{ old('business_type') === 'salon' ? 'selected' : '' }}>Salon de Coiffure / Barber</option>
                            <option value="clinic" {{ old('business_type') === 'clinic' ? 'selected' : '' }}>Dentiste / Clinique Médicale</option>
                            <option value="gym" {{ old('business_type') === 'gym' ? 'selected' : '' }}>Coach / Salle de Sport</option>
                            <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Autre Activité de Service</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Domain Setup -->
            <div>
                <h3 class="text-lg font-semibold border-b border-line pb-2 mb-4 text-clay">Domaine & Sous-domaine</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="subdomain" class="block text-sm font-semibold mb-2">Sous-domaine désiré</label>
                        <div class="flex">
                            <input type="text" name="subdomain" id="subdomain" required value="{{ old('subdomain') }}" placeholder="ex: bahja" class="flex-1 min-w-0 px-4 py-3 border border-line rounded-l-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            <span class="inline-flex items-center px-4 py-3 rounded-r-xl border border-l-0 border-line bg-line text-muted text-sm font-semibold">
                                .rdvpro.ma
                            </span>
                        </div>
                        <p class="mt-1.5 text-xs text-muted">Caractères alphanumériques uniquement (a-z, 0-9, tirets).</p>
                    </div>
                    <div>
                        <label for="custom_domain" class="block text-sm font-semibold mb-2">Domaine personnalisé (Optionnel)</label>
                        <input type="text" name="custom_domain" id="custom_domain" value="{{ old('custom_domain') }}" placeholder="Ex: www.monbarber.ma" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                        <p class="mt-1.5 text-xs text-muted">Le client devra faire pointer son DNS (A-Record) vers votre IP.</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Admin User Account -->
            <div>
                <h3 class="text-lg font-semibold border-b border-line pb-2 mb-4 text-clay">Compte Administrateur</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="admin_name" class="block text-sm font-semibold mb-2">Nom complet de l'admin</label>
                        <input type="text" name="admin_name" id="admin_name" required value="{{ old('admin_name') }}" placeholder="Ex: Karim Bahja" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                    <div>
                        <label for="admin_phone" class="block text-sm font-semibold mb-2">Téléphone admin</label>
                        <input type="text" name="admin_phone" id="admin_phone" value="{{ old('admin_phone') }}" placeholder="Ex: +212 661 00 00 00" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="admin_email" class="block text-sm font-semibold mb-2">Adresse E-mail admin</label>
                        <input type="email" name="admin_email" id="admin_email" required value="{{ old('admin_email') }}" placeholder="Ex: admin@bahja.ma" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                    <div>
                        <label for="admin_password" class="block text-sm font-semibold mb-2">Mot de passe provisoire</label>
                        <input type="password" name="admin_password" id="admin_password" required placeholder="Minimum 8 caractères" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->

            <div class="pt-4 border-t border-line flex justify-end space-x-4">
                <a href="{{ route('central.tenants.index') }}" class="px-5 py-3 rounded-xl border border-line bg-paper hover:bg-line transition text-sm font-semibold">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm">
                    Provisionner le client
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
