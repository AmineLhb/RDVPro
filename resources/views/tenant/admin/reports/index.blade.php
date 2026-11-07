@extends('tenant.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Rapports & Performance</h1>
            <p class="text-muted text-sm font-outfit mt-1">Analysez vos revenus, prestations populaires et performance du personnel.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.reports.pdf') }}" class="px-4 py-2.5 rounded-xl border border-line bg-card font-semibold text-ink hover:bg-paper transition-all duration-200 active:scale-95 text-sm font-outfit flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2 text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exporter en PDF
            </a>
            <a href="{{ route('tenant.reports.excel') }}" class="px-5 py-2.5 rounded-xl bg-olive text-paper font-semibold hover:bg-olive-2 transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exporter en Excel
            </a>
        </div>
    </div>

    <!-- Monthly Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="group relative bg-card border-t-4 border-t-clay border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Revenus cumulés</span>
                    <p class="text-3xl font-bold font-fraunces text-ink mt-2">
                        {{ number_format($metrics['total_revenue'], 2, ',', ' ') }} <span class="text-xs font-outfit font-normal text-muted">MAD</span>
                    </p>
                </div>
                <div class="p-2.5 bg-[#fbf9f4] border border-line rounded-xl text-clay group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Total généré</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-clay" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,25 Q15,10 30,20 T60,5 T90,15 T100,5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="group relative bg-card border-t-4 border-t-olive border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Rendez-vous</span>
                    <p class="text-3xl font-bold font-fraunces text-olive mt-2">{{ $metrics['total_bookings'] }}</p>
                </div>
                <div class="p-2.5 bg-[#f3f6f3] border border-line rounded-xl text-olive group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Total enregistrés</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-olive" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,15 Q20,28 40,10 T80,22 T100,2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <!-- New Customers Card -->
        <div class="group relative bg-card border-t-4 border-t-clay border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Nouveaux Clients</span>
                    <p class="text-3xl font-bold font-fraunces text-clay mt-2">{{ $metrics['new_customers_30days'] }}</p>
                </div>
                <div class="p-2.5 bg-[#fbf9f4] border border-line rounded-xl text-clay group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Derniers 30 jours (Total: {{ $metrics['total_customers'] }})</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-clay" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,28 L20,25 L40,18 L60,15 L80,5 L100,2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <!-- No Show Rate Card -->
        <div class="group relative bg-card border-t-4 border-t-gold border-x border-b border-line p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 ease-out active:scale-[0.99]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-muted text-xs font-semibold uppercase tracking-wider font-outfit">Taux d'absence</span>
                    <p class="text-3xl font-bold font-fraunces text-ink mt-2">{{ $metrics['no_show_rate'] }}%</p>
                </div>
                <div class="p-2.5 bg-[#fdfaf2] border border-line rounded-xl text-gold group-hover:scale-110 duration-300 ease-out transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-muted font-outfit">Moyenne 30 derniers jours</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,2 Q20,20 40,8 T80,18 T100,25" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts Simulated Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Trend Chart (Simulated HTML chart) -->
        <div class="bg-card border border-line rounded-2xl shadow-sm p-6 lg:col-span-2 flex flex-col justify-between">
            <div class="border-b border-line pb-3 mb-6">
                <h3 class="text-lg font-semibold text-ink">Évolution des revenus (6 derniers mois)</h3>
            </div>
            
            <!-- Simulated Bar Chart -->
            <div class="flex items-end justify-between h-56 pt-4 px-4 border-l border-b border-line">
                @foreach($revenueTrend as $t)
                    <div class="flex flex-col items-center flex-1 group">
                        <!-- Bar -->
                        <div class="w-12 bg-clay hover:bg-clay-soft transition-all rounded-t-lg relative" style="height: {{ max(10, min(180, ($t['revenue'] / 5000) * 150)) }}px">
                            <span class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-ink text-paper text-[10px] px-1.5 py-0.5 rounded font-mono opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                {{ number_format($t['revenue'], 0) }} DH
                            </span>
                        </div>
                        <!-- Label -->
                        <span class="text-xs text-muted mt-2 font-semibold capitalize">{{ $t['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status Breakdown (Simulated Pie chart representation) -->
        <div class="bg-card border border-line rounded-2xl shadow-sm p-6 flex flex-col justify-between">
            <div class="border-b border-line pb-3 mb-4">
                <h3 class="text-lg font-semibold text-ink">Répartition des statuts</h3>
            </div>
            
            <div class="space-y-4">
                @foreach($statusBreakdown as $sb)
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="capitalize text-ink">{{ $sb['status'] }}</span>
                            <span class="text-muted">{{ $sb['count'] }} ({{ $sb['percentage'] }}%)</span>
                        </div>
                        <div class="w-full bg-paper h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full 
                                @if($sb['status'] === 'completed') bg-green-700
                                @elseif($sb['status'] === 'confirmed') bg-olive
                                @elseif($sb['status'] === 'pending') bg-gold
                                @elseif($sb['status'] === 'no_show') bg-red-700
                                @else bg-gray-400
                                @endif" 
                                style="width: {{ $sb['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Services & Staff performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Top services -->
        <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-line">
                <h3 class="text-lg font-semibold text-ink">Prestations les plus demandées</h3>
            </div>
            <div class="divide-y divide-line flex-1">
                @forelse($topServices as $index => $ts)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="font-serif font-bold text-lg text-clay-soft">{{ $index + 1 }}</span>
                            <span class="font-bold text-ink">{{ $ts['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-ink">{{ $ts['count'] }} réservations</p>
                            <p class="text-xs text-muted">{{ number_format($ts['revenue'], 2) }} MAD générés</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-muted">Aucune donnée disponible.</div>
                @endforelse
            </div>
        </div>

        <!-- Staff performance -->
        <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-line">
                <h3 class="text-lg font-semibold text-ink">Performance de l'équipe</h3>
            </div>
            <div class="divide-y divide-line flex-1">
                @forelse($staffPerformance as $sp)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-ink">{{ $sp['name'] }}</p>
                            <p class="text-xs text-muted capitalize">{{ $sp['role'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-olive">{{ number_format($sp['revenue'], 2) }} MAD</p>
                            <p class="text-xs text-muted">{{ $sp['count'] }} prestations complétées</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-muted">Aucune donnée disponible.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
