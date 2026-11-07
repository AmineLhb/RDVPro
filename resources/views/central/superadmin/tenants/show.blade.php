@extends('central.layout')

@section('content')
<div class="space-y-8 max-w-6xl">
    <!-- Breadcrumb & Back Button -->
    <div>
        <a href="{{ route('central.tenants.index') }}" class="text-sm text-muted hover:text-ink font-semibold flex items-center mb-2 font-outfit">
            ← Retour à la liste des clients
        </a>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Gérer le client : {{ $tenant->name }}</h1>
        <p class="text-muted text-sm font-outfit mt-1">Consultez les détails, gérez l'accès et validez les abonnements de ce compte client.</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Business & Domain Profile (1/3 width) -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Profile Card -->
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-clay border-b border-line pb-2 mb-4 font-serif">Profil de l'entreprise</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-muted block text-xs uppercase">ID Unique</span>
                        <span class="font-mono font-bold text-ink">{{ $tenant->id }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs uppercase">Nom de l'établissement</span>
                        <span class="font-bold text-ink">{{ $tenant->name }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs uppercase">Secteur d'activité</span>
                        <span class="capitalize text-ink">{{ $tenant->business_type }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs uppercase">Date de création</span>
                        <span class="text-ink">{{ $tenant->created_at ? $tenant->created_at->format('d/m/Y \à H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Domains Card -->
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-clay border-b border-line pb-2 mb-4 font-serif">Domaines & Liens</h3>
                <div class="space-y-3">
                    @forelse($tenant->domains as $domain)
                        <div class="p-3 bg-paper border border-line rounded-xl flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-ink truncate mr-2">{{ $domain->domain }}</span>
                            <a href="http://{{ $domain->domain }}:8000" target="_blank" class="text-xs text-olive hover:underline font-bold shrink-0">
                                Visiter ↗
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-muted">Aucun domaine configuré.</p>
                    @endforelse
                </div>
            </div>

            <!-- Access Status Card -->
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-clay border-b border-line pb-2 mb-4 font-serif">Statut du compte</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold">Statut d'accès :</span>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $tenant->status === 'active' ? 'bg-[#d1e7dd] text-olive' : 'bg-[#f8d7da] text-red-800' }}">
                        {{ $tenant->status === 'active' ? 'Actif' : 'Suspendu' }}
                    </span>
                </div>
                <form action="{{ route('central.tenants.toggle', $tenant->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold border border-line bg-paper hover:bg-line text-ink transition shadow-sm">
                        {{ $tenant->status === 'active' ? 'Suspendre l\'accès au compte' : 'Activer l\'accès au compte' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Subscription & Receipt validation (2/3 width) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Subscription Dates Card -->
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-clay border-b border-line pb-2 mb-4 font-serif">Abonnement & Échéance</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="p-4 bg-paper rounded-xl border border-line">
                        <span class="text-muted block text-xs uppercase font-semibold">Statut global</span>
                        <span class="text-base font-black mt-1 block">
                            @if($tenant->isSubscriptionActive())
                                <span class="text-olive">Actif ({{ $tenant->daysLeftForSubscription() }} jours restants)</span>
                            @else
                                <span class="text-red-800 font-bold">Expiré / Inactif</span>
                            @endif
                        </span>
                    </div>
                    <div class="p-4 bg-paper rounded-xl border border-line">
                        <span class="text-muted block text-xs uppercase font-semibold">Fin d'essai gratuit</span>
                        <span class="text-base font-bold mt-1 block text-ink">
                            {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="p-4 bg-paper rounded-xl border border-line">
                        <span class="text-muted block text-xs uppercase font-semibold">Fin d'abonnement payant</span>
                        <span class="text-base font-bold mt-1 block text-ink">
                            {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : 'Aucun actif' }}
                        </span>
                    </div>
                </div>

                <!-- Receipt review section -->
                @if($tenant->receipt_path)
                    <div class="border-t border-line pt-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                            <div>
                                <h4 class="text-sm font-bold text-ink uppercase tracking-wider">Reçu de virement envoyé</h4>
                                <p class="text-xs text-muted mt-1">
                                    Téléversé le : {{ $tenant->receipt_uploaded_at ? $tenant->receipt_uploaded_at->format('d/m/Y à H:i') : 'N/A' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold text-muted">Plan choisi :</span>
                                <span class="px-3 py-1 bg-clay text-paper text-xs font-black uppercase rounded-lg">
                                    @if($tenant->selected_plan === '1_month') 1 Mois @elseif($tenant->selected_plan === '3_months') 3 Mois @elseif($tenant->selected_plan === '1_year') 1 An @else N/A @endif
                                </span>
                            </div>
                        </div>

                        <!-- Embedded viewer -->
                        <div class="mb-6 border border-line rounded-2xl bg-paper overflow-hidden p-4">
                            <p class="text-xs font-bold text-muted mb-2 uppercase">Aperçu du reçu :</p>
                            @if(str_ends_with(strtolower($tenant->receipt_path), '.pdf'))
                                <iframe src="{{ asset($tenant->receipt_path) }}" class="w-full h-[55vh] border border-line rounded-xl shadow-inner bg-white" frameborder="0"></iframe>
                            @else
                                <div class="flex justify-center bg-white p-4 rounded-xl border border-line">
                                    <img src="{{ asset($tenant->receipt_path) }}" alt="Reçu" class="max-w-full max-h-[50vh] object-contain rounded shadow-sm">
                                </div>
                            @endif
                            <div class="mt-2 text-right">
                                <a href="{{ asset($tenant->receipt_path) }}" target="_blank" class="text-xs text-olive hover:underline font-bold inline-flex items-center">
                                    Télécharger le fichier original 📥
                                </a>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($tenant->receipt_status === 'pending')
                            <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h5 class="text-amber-800 font-bold text-sm">Paiement en attente de vérification</h5>
                                    <p class="text-xs text-amber-700 mt-1">Veuillez valider si le montant sur le virement correspond au plan sélectionné.</p>
                                </div>
                                <div class="flex gap-3 shrink-0">
                                    <!-- Reject Form -->
                                    <form action="{{ route('central.tenants.reject-receipt', $tenant->id) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="confirmActionCentral(this, {title: 'Rejeter le reçu ?', text: 'Êtes-vous sûr de vouloir rejeter ce reçu ?', icon: 'warning', confirmText: 'Oui, rejeter', confirmColor: '#c2683f'})" class="px-5 py-2.5 bg-red-800 text-paper font-bold rounded-xl hover:bg-red-900 transition shadow-sm text-xs">
                                            Rejeter le reçu
                                        </button>
                                    </form>

                                    <!-- Approve Form -->
                                    <form action="{{ route('central.tenants.approve-receipt', $tenant->id) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="confirmActionCentral(this, {title: 'Valider le paiement ?', text: 'Confirmez-vous la réception du virement ?', icon: 'question', confirmText: 'Oui, valider', confirmColor: '#3d5a3d'})" class="px-5 py-2.5 bg-olive text-paper font-bold rounded-xl hover:bg-olive-2 transition shadow-sm text-xs">
                                            Valider le paiement
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($tenant->receipt_status === 'approved')
                            <div class="p-4 bg-[#d1e7dd] text-olive border border-line rounded-2xl font-semibold text-sm">
                                ✅ Ce paiement a été validé par l'administrateur. L'abonnement a été mis à jour.
                            </div>
                        @elseif($tenant->receipt_status === 'rejected')
                            <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-2xl font-semibold text-sm">
                                ❌ Le reçu de paiement a été rejeté par l'administrateur. Le client doit en téléverser un autre.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="border-t border-line pt-6 text-center py-8 text-muted">
                        <svg class="mx-auto h-12 w-12 text-muted/60 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm font-semibold">Aucun reçu de paiement envoyé pour l'instant.</p>
                        <p class="text-xs text-muted mt-1">Le client n'a pas encore initié de processus d'abonnement ou de téléversement.</p>
                    </div>
                @endif
            </div>

            <!-- Danger Zone -->
            <div class="bg-card border border-red-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-red-800 border-b border-red-100 pb-2 mb-4 font-serif">Zone de danger</h3>
                <p class="text-xs text-muted mb-4">La suppression du client est définitive et effacera toutes ses données, rendez-vous, clients, configurations et domaines de manière irréversible.</p>
                <form action="{{ route('central.tenants.force-destroy', $tenant->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmActionCentral(this, {title: 'Suppression Définitive !', text: 'ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement ce client et toutes ses données ? Cette action est irréversible !', icon: 'error', confirmText: 'Oui, supprimer définitivement', confirmColor: '#991b1b'})" class="px-5 py-2.5 bg-red-800 hover:bg-red-900 text-paper text-xs font-bold rounded-xl transition shadow-sm">
                        Supprimer définitivement le client et ses données
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
