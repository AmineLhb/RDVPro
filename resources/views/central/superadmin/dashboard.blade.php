@extends('central.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Tableau de bord</h1>
            <p class="text-muted text-sm font-outfit mt-1">Vue d'ensemble de vos clients multi-tenant RDVPro.</p>
        </div>
        <a href="{{ route('central.tenants.create') }}" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Créer un client
        </a>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Clients Card -->
        <div class="group relative bg-card border-t-4 border-t-clay border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Total Clients</span>
                    <p class="text-4xl font-bold font-fraunces text-ink mt-2">{{ $totalTenants }}</p>
                </div>
                <div class="p-2.5 bg-[#fbf9f4] border border-line rounded-xl text-clay group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Base clients globale</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-clay" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,25 Q15,10 30,20 T60,5 T90,15 T100,5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <!-- Active Clients Card -->
        <div class="group relative bg-card border-t-4 border-t-olive border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Clients Actifs</span>
                    <p class="text-4xl font-bold font-fraunces text-olive mt-2">{{ $activeTenants }}</p>
                </div>
                <div class="p-2.5 bg-[#f3f6f3] border border-line rounded-xl text-olive group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Abonnements actifs</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-olive" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,28 L20,25 L40,18 L60,15 L80,5 L100,2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <!-- Suspended Clients Card -->
        <div class="group relative bg-card border-t-4 border-t-gold border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Clients Suspendus</span>
                    <p class="text-4xl font-bold font-fraunces text-clay mt-2">{{ $suspendedTenants }}</p>
                </div>
                <div class="p-2.5 bg-[#fdfaf2] border border-line rounded-xl text-gold group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Factures en attente</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,2 Q20,20 40,8 T80,18 T100,25" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent tenants table -->
    <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-line">
            <h3 class="text-lg font-bold font-fraunces text-ink">Clients Récents</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-paper border-b border-line text-muted uppercase text-xs tracking-wider font-outfit">
                        <th class="p-4">Entreprise</th>
                        <th class="p-4">Type d'activité</th>
                        <th class="p-4">Domaine principal</th>
                        <th class="p-4">Créé le</th>
                        <th class="p-4">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-[#FAF8F5] transition duration-300 group border-l-4 
                            @if($tenant->status === 'active') border-l-olive
                            @else border-l-clay
                            @endif">
                            <td class="p-4 font-bold text-ink font-outfit group-hover:text-olive transition">{{ $tenant->name }}</td>
                            <td class="p-4 capitalize font-outfit text-muted">{{ $tenant->business_type }}</td>
                            <td class="p-4 font-outfit">
                                @if($tenant->domains->first())
                                    <a href="http://{{ $tenant->domains->first()->domain }}:8000" target="_blank" class="text-olive hover:underline font-semibold flex items-center space-x-1">
                                        <span>{{ $tenant->domains->first()->domain }}</span>
                                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @else
                                    <span class="text-muted text-xs">Aucun domaine</span>
                                @endif
                            </td>
                            <td class="p-4 text-muted font-outfit">{{ $tenant->created_at->format('d/m/Y') }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full font-outfit tracking-wide uppercase
                                    @if($tenant->status === 'active') bg-olive/10 text-olive border border-olive/20
                                    @else bg-clay/10 text-clay border border-clay/20
                                    @endif">
                                    {{ $tenant->status === 'active' ? 'Actif' : 'Suspendu' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-muted font-outfit">Aucun client trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
