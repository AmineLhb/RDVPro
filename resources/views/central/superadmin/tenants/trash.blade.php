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
                deleted_at: '{{ $t->deleted_at ? $t->deleted_at->format('d/m/Y \à H:i') : '' }}',
                deleted_timestamp: {{ $t->deleted_at ? $t->deleted_at->timestamp : 0 }},
                domains: [
                    @foreach($t->domains as $d)
                    '{{ $d->domain }}',
                    @endforeach
                ],
                restore_url: '{{ route('central.tenants.restore', $t->id) }}',
                destroy_url: '{{ route('central.tenants.force-destroy', $t->id) }}',
                csrf: '{{ csrf_token() }}'
            },
            @endforeach
        ],
        search: '',
        sortBy: 'name',
        sortAsc: true,
        page: 1,
        perPage: 10,
        
        get filteredTenants() {
            return this.tenants.filter(t => {
                return t.name.toLowerCase().includes(this.search.toLowerCase()) || 
                       t.id.toLowerCase().includes(this.search.toLowerCase());
            }).sort((a, b) => {
                let valA = a[this.sortBy];
                let valB = b[this.sortBy];
                
                if (this.sortBy === 'deleted_at') {
                    valA = a.deleted_timestamp;
                    valB = b.deleted_timestamp;
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
    <div>
        <a href="{{ route('central.tenants.index') }}" class="text-sm text-muted hover:text-ink font-semibold flex items-center mb-2 font-outfit">
            ← Retour aux clients actifs
        </a>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Clients Supprimés (Corbeille)</h1>
        <p class="text-muted text-sm font-outfit mt-1">Gérez les comptes clients temporairement supprimés. Vous pouvez les restaurer ou les détruire définitivement.</p>
    </div>

    <!-- Controls -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Search bar -->
        <div class="w-full md:w-1/3">
            <input type="text" x-model="search" @input="page = 1" placeholder="Rechercher par nom ou ID..." class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
        </div>
        
        <!-- Per Page Select -->
        <div class="flex items-center space-x-1.5 shrink-0">
            <span class="text-xs text-muted">Par page:</span>
            <select x-model="perPage" @change="page = 1" class="px-2 py-2 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-xs font-semibold">
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
            </select>
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
                        <th class="p-4">Type</th>
                        <th class="p-4 cursor-pointer hover:bg-line/20 transition" @click="toggleSort('deleted_at')">
                            <div class="flex items-center space-x-1">
                                <span>Date Suppression</span>
                                <template x-if="sortBy === 'deleted_at'">
                                    <span x-text="sortAsc ? '▲' : '▼'" class="text-[9px] text-clay"></span>
                                </template>
                            </div>
                        </th>
                        <th class="p-4">Sous-domaine</th>
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
                            <!-- Deletion Date -->
                            <td class="p-4 text-sm text-red-800 font-semibold" x-text="tenant.deleted_at"></td>
                            <!-- Domain links -->
                            <td class="p-4 text-xs font-mono text-muted">
                                <template x-for="dom in tenant.domains">
                                    <span class="block mb-0.5" x-text="dom"></span>
                                </template>
                            </td>
                            <!-- Actions -->
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <!-- Restore Form -->
                                <form :action="tenant.restore_url" method="POST" class="inline">
                                    <input type="hidden" name="_token" :value="tenant.csrf">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-olive text-paper hover:bg-olive-2 transition shadow-sm">
                                        Restaurer
                                    </button>
                                </form>

                                <!-- Permanent Delete Form -->
                                <form :action="tenant.destroy_url" method="POST" class="inline">
                                    <input type="hidden" name="_token" :value="tenant.csrf">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" onclick="confirmActionCentral(this, {title: 'Suppression Définitive !', text: 'ATTENTION : Êtes-vous sûr de vouloir supprimer DEFINITIVEMENT ce client ? Cette action est irreversible, elle supprimera toutes ses donnees, rendez-vous, clients et base de donnees !', icon: 'error', confirmText: 'Oui, supprimer définitivement', confirmColor: '#991b1b'})" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-800 text-paper hover:bg-red-900 transition">
                                        Supprimer Définitivement
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <!-- Empty State -->
                    <template x-if="filteredTenants.length === 0">
                        <tr>
                            <td colspan="5" class="p-8 text-center text-muted">La corbeille est vide.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 bg-paper border-t border-line flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-muted" x-text="'Affichage de ' + (filteredTenants.length > 0 ? (page - 1) * perPage + 1 : 0) + ' à ' + Math.min(page * perPage, filteredTenants.length) + ' sur ' + filteredTenants.length + ' clients supprimés'">
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
