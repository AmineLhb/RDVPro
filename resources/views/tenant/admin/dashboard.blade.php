@extends('tenant.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Tableau de bord</h1>
            <p class="text-muted text-sm font-outfit mt-1">Aujourd'hui, nous sommes le {{ now()->translatedFormat('d F Y') }}.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.customers.index') }}?create=1" class="px-5 py-2.5 rounded-xl border border-line bg-card font-semibold text-ink hover:bg-paper transition-all duration-200 active:scale-95 text-sm font-outfit flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2 text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Nouveau client
            </a>
            <a href="{{ route('tenant.appointments.index') }}?create=1" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit flex items-center">
                <svg class="w-4 h-4 mr-2 text-paper/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouveau rendez-vous
            </a>
        </div>
    </div>

    <!-- Stats row -->
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
                <span class="text-xs text-muted font-outfit">Aujourd'hui : <span class="font-bold text-ink">{{ number_format($metrics['todays_revenue'], 2, ',', ' ') }} MAD</span></span>
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
                <span class="text-xs text-muted font-outfit">Aujourd'hui : <span class="font-bold text-ink">{{ $metrics['todays_bookings'] }}</span></span>
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
                <span class="text-xs text-muted font-outfit">Total global : <span class="font-bold text-ink">{{ $metrics['total_customers'] }}</span></span>
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
                <span class="text-xs text-muted font-outfit">Moyenne 30 jours</span>
                <!-- Sparkline -->
                <svg class="w-16 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 100 30" stroke-width="1.5">
                    <path d="M0,2 Q20,20 40,8 T80,18 T100,25" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Dynamic Section: Bookings and Dispatch Simulator Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upcoming appointments -->
        <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden lg:col-span-2 flex flex-col">
            <div class="p-6 border-b border-line flex items-center justify-between">
                <h3 class="text-lg font-bold font-fraunces text-ink">Prochains rendez-vous</h3>
                <a href="{{ route('tenant.appointments.index') }}" class="text-sm text-olive hover:underline font-semibold font-outfit">Voir tout →</a>
            </div>
            <div class="flex-1 divide-y divide-line">
                @forelse($upcomingAppointments as $app)
                    <div class="p-5 flex items-center justify-between hover:bg-[#FAF8F5] transition duration-300 group border-l-4 
                        @if($app->status === 'confirmed') border-l-olive
                        @elseif($app->status === 'pending') border-l-gold
                        @elseif($app->status === 'completed') border-l-[#2c7a7b]
                        @elseif($app->status === 'no_show') border-l-clay
                        @else border-l-gray-300
                        @endif">
                        <div class="flex items-center space-x-4">
                            <!-- Time Badge -->
                            <div class="px-3 py-2 bg-[#f0eae1] border border-line rounded-xl text-center min-w-[75px] group-hover:bg-[#fffdf9] transition duration-300">
                                <p class="text-[10px] text-muted uppercase font-bold tracking-wider font-outfit">{{ $app->starts_at->translatedFormat('d M') }}</p>
                                <p class="text-base font-bold text-ink font-fraunces">{{ $app->starts_at->format('H:i') }}</p>
                            </div>
                            <!-- Details -->
                            <div>
                                <p class="font-bold text-ink group-hover:text-olive transition duration-300 font-outfit">{{ $app->customer->name ?? 'Client inconnu' }}</p>
                                <p class="text-xs text-muted font-outfit">{{ $app->service->name ?? 'Prestation' }} <span class="text-line mx-1">·</span> {{ $app->resource->name ?? 'Staff' }}</p>
                            </div>
                        </div>
                        <!-- Status and actions -->
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full font-outfit tracking-wide uppercase
                                @if($app->status === 'confirmed') bg-olive/10 text-olive border border-olive/20
                                @elseif($app->status === 'pending') bg-gold/10 text-gold border border-gold/20
                                @elseif($app->status === 'completed') bg-[#d1e7dd] text-green-800 border border-green-200
                                @elseif($app->status === 'no_show') bg-clay/10 text-clay border border-clay/20
                                @else bg-gray-100 text-gray-700 border border-gray-200
                                @endif">
                                {{ ucfirst($app->status) }}
                            </span>
                            <form action="{{ route('tenant.appointments.status', $app->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="border border-line rounded-xl text-xs px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-olive/50 bg-card text-ink font-outfit shadow-sm cursor-pointer hover:bg-paper transition">
                                    <option value="">Modifier</option>
                                    <option value="confirmed" {{ $app->status === 'confirmed' ? 'selected' : '' }}>Confirmer</option>
                                    <option value="completed" {{ $app->status === 'completed' ? 'selected' : '' }}>Terminer</option>
                                    <option value="no_show" {{ $app->status === 'no_show' ? 'selected' : '' }}>No-show</option>
                                    <option value="cancelled" {{ $app->status === 'cancelled' ? 'selected' : '' }}>Annuler</option>
                                </select>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-muted font-outfit">Aucun rendez-vous à venir.</div>
                @endforelse
            </div>
        </div>

        <!-- Simulated Dispatch Log Viewer -->
        <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-line flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <!-- Dynamic Pulse Dot -->
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-olive opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-olive"></span>
                    </span>
                    <h3 class="text-lg font-bold font-fraunces text-ink">Simulateur d'Envoi</h3>
                </div>
                <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase rounded-full bg-olive/15 text-olive border border-olive/30">Actif</span>
            </div>
            <div class="p-5 flex-1 overflow-y-auto space-y-4 max-h-[400px] bg-[#fdfbf7]">
                @forelse($logs as $log)
                    <div class="p-4 bg-card border border-line rounded-xl space-y-3 shadow-sm hover:shadow transition duration-300 group">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-ink truncate font-outfit group-hover:text-olive transition">{{ $log->customer_name }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider font-outfit
                                @if($log->channel === 'whatsapp') bg-[#25d366]/10 text-[#20ba5a] border border-[#25d366]/20
                                @else bg-purple-100 text-purple-800 border border-purple-200
                                @endif">
                                {{ $log->channel }}
                            </span>
                        </div>
                        <p class="text-xs text-muted leading-relaxed italic whitespace-pre-line bg-[#fffdf9] p-3 rounded-lg border border-line/50 font-outfit">{{ $log->message_body }}</p>
                        <div class="flex justify-between items-center text-[10px] text-muted/80 pt-1 border-t border-line/40 font-outfit">
                            <span class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-olive mr-1"></span>
                                Status: {{ $log->status }}
                            </span>
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-muted text-xs font-outfit">Aucun message envoyé pour le moment. Créez un rendez-vous pour voir l'envoi simulé.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
