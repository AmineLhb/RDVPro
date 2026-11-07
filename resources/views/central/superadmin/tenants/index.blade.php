@extends('central.layout')

@section('content')
<div class="space-y-8" 
     x-data="{
        tenants: [
            @foreach($tenants as $t)
            {
                id: '{{ $t->id }}',
                name: '{{ addslashes($t->name) }}',
                business_type: '{{ $t->business_type }}',
                locale: '{{ strtoupper($t->locale) }}',
                status: '{{ $t->status }}',
                receipt_status: '{{ $t->receipt_status }}',
                is_active: {{ $t->isSubscriptionActive() ? 'true' : 'false' }},
                days_left: {{ $t->daysLeftForSubscription() }},
                trial_date: '{{ $t->trial_ends_at ? $t->trial_ends_at->format('d/m/Y') : '' }}',
                trial_timestamp: {{ $t->trial_ends_at ? $t->trial_ends_at->timestamp : 0 }},
                sub_date: '{{ $t->subscription_ends_at ? $t->subscription_ends_at->format('d/m/Y') : '' }}',
                sub_timestamp: {{ $t->subscription_ends_at ? $t->subscription_ends_at->timestamp : 0 }},
                domains: [
                    @foreach($t->domains as $d)
                    '{{ $d->domain }}',
                    @endforeach
                ],
                show_url: '{{ route('central.tenants.show', $t->id) }}',
                destroy_url: '{{ route('central.tenants.destroy', $t->id) }}',
                csrf: '{{ csrf_token() }}'
            },
            @endforeach
        ],
        search: '',
        businessType: 'all',
        subStatus: 'all',
        accessStatus: 'all',
        sortBy: 'name',
        sortAsc: true,
        page: 1,
        perPage: 10,
        
        get filteredTenants() {
            return this.tenants.filter(t => {
                const matchesSearch = t.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                      t.id.toLowerCase().includes(this.search.toLowerCase());
                const matchesBusiness = this.businessType === 'all' || t.business_type === this.businessType;
                const matchesAccess = this.accessStatus === 'all' || t.status === this.accessStatus;
                
                let matchesSub = true;
                if (this.subStatus === 'pending') {
                    matchesSub = t.receipt_status === 'pending';
                } else if (this.subStatus === 'active') {
                    matchesSub = t.is_active;
                } else if (this.subStatus === 'expired') {
                    matchesSub = !t.is_active;
                }
                
                return matchesSearch && matchesBusiness && matchesAccess && matchesSub;
            }).sort((a, b) => {
                let valA = a[this.sortBy];
                let valB = b[this.sortBy];
                
                if (this.sortBy === 'trial_date') {
                    valA = a.trial_timestamp;
                    valB = b.trial_timestamp;
                } else if (this.sortBy === 'status') {
                    valA = a.status;
                    valB = b.status;
                }
                
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });
        },
        
        get paginatedTenants() {
            const start = (this.page - 1) * this.perPage;
            return this.filteredTenants.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredTenants.length / this.perPage) || 1;
        },
        
        toggleSort(field) {
            if (this.sortBy === field) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortBy = field;
                this.sortAsc = true;
            }
            this.page = 1;
        }
     }">
     
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Clients (Tenants)</h1>
            <p class="text-muted text-sm font-outfit mt-1">Gérez les comptes clients de votre application SaaS.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('central.tenants.trash') }}" class="px-5 py-2.5 rounded-xl border border-line bg-paper font-semibold hover:bg-line transition-all duration-200 active:scale-95 text-sm font-outfit flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2 text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Corbeille
            </a>
            <a href="{{ route('central.tenants.create') }}" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Créer un client
            </a>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Search bar -->
        <div class="w-full md:w-1/3">
            <input type="text" x-model="search" @input="page = 1" placeholder="Rechercher par nom ou ID..." class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
        </div>
        
        <!-- Filters select options -->
        <div class="w-full md:w-auto flex flex-wrap md:flex-nowrap gap-3 items-center justify-end">
            <!-- Business Type Filter -->
            <div>
                <select x-model="businessType" @change="page = 1" class="px-3 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-xs font-semibold">
                    <option value="all">Toutes activités</option>
                    <option value="salon">Salon / Barber</option>
                    <option value="clinic">Dentiste / Clinique</option>
                    <option value="gym">Salle de sport / Coach</option>
                    <option value="other">Autre</option>
                </select>
            </div>

            <!-- Subscription Status Filter -->
            <div>
                <select x-model="subStatus" @change="page = 1" class="px-3 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-xs font-semibold">
                    <option value="all">Tous abonnements</option>
                    <option value="active">Actif</option>
                    <option value="expired">Expiré</option>
                    <option value="pending">Reçu en attente</option>
                </select>
            </div>

            <!-- Access Status Filter -->
            <div>
                <select x-model="accessStatus" @change="page = 1" class="px-3 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-xs font-semibold">
                    <option value="all">Tous statuts accès</option>
                    <option value="active">Actif</option>
                    <option value="suspended">Suspendu</option>
                </select>
            </div>

            <!-- Per Page Select -->
            <div class="flex items-center space-x-1.5 shrink-0 ml-2">
                <span class="text-xs text-muted">Par page:</span>
                <select x-model="perPage" @change="page = 1" class="px-2 py-2 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-xs font-semibold">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tenants table -->
    <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-paper border-b border-line text-muted uppercase text-xs tracking-wider">
                        <th class="p-4 cursor-pointer hover:bg-line/20 transition" @click="toggleSort('name')">
                            <div class="flex items-center space-x-1">
                                <span>Entreprise / Nom</span>
                                <template x-if="sortBy === 'name'">
                                    <span x-text="sortAsc ? '▲' : '▼'" class="text-[9px] text-clay"></span>
                                </template>
                            </div>
                        </th>
                        <th class="p-4 cursor-pointer hover:bg-line/20 transition" @click="toggleSort('business_type')">
                            <div class="flex items-center space-x-1">
                                <span>Type</span>
                                <template x-if="sortBy === 'business_type'">
                                    <span x-text="sortAsc ? '▲' : '▼'" class="text-[9px] text-clay"></span>
                                </template>
                            </div>
                        </th>
                        <th class="p-4">Langue</th>
                        <th class="p-4 cursor-pointer hover:bg-line/20 transition" @click="toggleSort('trial_date')">
                            <div class="flex items-center space-x-1">
                                <span>Abonnement</span>
                                <template x-if="sortBy === 'trial_date'">
                                    <span x-text="sortAsc ? '▲' : '▼'" class="text-[9px] text-clay"></span>
                                </template>
                            </div>
                        </th>
                        <th class="p-4">Sous-domaine / Lien</th>
                        <th class="p-4 cursor-pointer hover:bg-line/20 transition" @click="toggleSort('status')">
                            <div class="flex items-center space-x-1">
                                <span>Statut Accès</span>
                                <template x-if="sortBy === 'status'">
                                    <span x-text="sortAsc ? '▲' : '▼'" class="text-[9px] text-clay"></span>
                                </template>
                            </div>
                        </th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <template x-for="tenant in paginatedTenants" :key="tenant.id">
                        <tr class="hover:bg-paper/50">
                            <!-- Client name & ID -->
                            <td class="p-4">
                                <p class="font-bold text-ink" x-text="tenant.name"></p>
                                <p class="text-xs text-muted" x-text="'ID: ' + tenant.id"></p>
                            </td>
                            <!-- Business Type -->
                            <td class="p-4 capitalize text-sm" x-text="tenant.business_type"></td>
                            <!-- Language -->
                            <td class="p-4 text-sm uppercase font-semibold" x-text="tenant.locale"></td>
                            <!-- Subscription status -->
                            <td class="p-4 text-sm">
                                <template x-if="tenant.receipt_status === 'pending'">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Reçu en attente ⏳
                                    </span>
                                </template>
                                <template x-if="tenant.receipt_status !== 'pending' && tenant.is_active">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#d1e7dd] text-olive border border-olive/10" x-text="'Actif (' + tenant.days_left + ' j)'">
                                    </span>
                                </template>
                                <template x-if="tenant.receipt_status !== 'pending' && !tenant.is_active">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#f8d7da] text-red-800 border border-red-200">
                                        Expiré ⚠️
                                    </span>
                                </template>
                            </td>
                            <!-- Domain links -->
                            <td class="p-4">
                                <template x-for="dom in tenant.domains">
                                    <a :href="'http://' + dom + ':8000'" target="_blank" class="text-olive hover:underline block font-semibold text-xs mb-0.5" x-text="dom + ' ↗'">
                                    </a>
                                </template>
                            </td>
                            <!-- Account Access Status -->
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                      :class="tenant.status === 'active' ? 'bg-[#d1e7dd] text-olive' : 'bg-[#f8d7da] text-red-800'"
                                      x-text="tenant.status === 'active' ? 'Actif' : 'Suspendu'">
                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <a :href="tenant.show_url" class="inline-block px-3 py-1.5 rounded-lg text-xs font-semibold bg-olive text-paper hover:bg-olive-2 transition shadow-sm">
                                    Gérer
                                </a>

                                <form :action="tenant.destroy_url" method="POST" class="inline">
                                    <input type="hidden" name="_token" :value="tenant.csrf">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" onclick="confirmActionCentral(this, {title: 'Supprimer ce client ?', text: 'Êtes-vous sûr de vouloir placer ce client dans la corbeille ?', icon: 'warning', confirmText: 'Oui, supprimer', confirmColor: '#c2683f'})" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-800 text-paper hover:bg-red-900 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <!-- Empty State -->
                    <template x-if="filteredTenants.length === 0">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-muted">Aucun client trouvé pour ces critères.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 bg-paper border-t border-line flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-muted" x-text="'Affichage de ' + (filteredTenants.length > 0 ? (page - 1) * perPage + 1 : 0) + ' à ' + Math.min(page * perPage, filteredTenants.length) + ' sur ' + filteredTenants.length + ' clients'">
            </span>
            
            <div class="flex items-center space-x-2">
                <button @click="page > 1 ? page-- : null" 
                        :disabled="page === 1"
                        :class="page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-line text-ink'"
                        class="px-3 py-1.5 border border-line bg-card rounded-lg text-xs font-semibold transition">
                    Précédent
                </button>
                <span class="text-xs font-bold text-ink" x-text="page + ' / ' + totalPages"></span>
                <button @click="page < totalPages ? page++ : null" 
                        :disabled="page === totalPages"
                        :class="page === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-line text-ink'"
                        class="px-3 py-1.5 border border-line bg-card rounded-lg text-xs font-semibold transition">
                    Suivant
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
