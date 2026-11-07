<!DOCTYPE html>
<html lang="fr" class="h-full bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RDVPro Admin - {{ tenant('name') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Play Local Asset -->
    <script src="{{ global_asset('vendor/tailwindcss/tailwind.js') }}"></script>
    <script src="{{ global_asset('js/tailwind-config.js') }}"></script>

    <!-- Custom CSS (extracted inline styles) -->
    <link rel="stylesheet" href="{{ global_asset('css/app.css') }}">

    <!-- Flatpickr CSS & JS Local Assets -->
    <link rel="stylesheet" href="{{ global_asset('vendor/flatpickr/flatpickr.min.css') }}">
    <script src="{{ global_asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ global_asset('vendor/flatpickr/fr.js') }}"></script>

    <!-- SweetAlert2 (JS & CSS bundle) -->
    <script src="{{ global_asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        if (window.Swal) {
            window.Swal = window.Swal.mixin({
                heightAuto: false
            });
        }
    </script>
    <script src="{{ global_asset('js/app.js') }}"></script>

    <!-- Alpine.js Local Asset -->
    <script defer src="{{ global_asset('vendor/alpinejs/alpine.min.js') }}"></script>

    <style>
        html.swal2-height-auto, html.swal2-shown, body.swal2-shown {
            height: 100% !important;
        }
    </style>
</head>
<body class="h-full text-ink flex overflow-hidden">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:flex-shrink-0 bg-ink text-paper w-64 flex-col justify-between">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Brand -->
            <div class="flex items-center h-16 px-6 bg-ink border-b border-[#2a201a]">
                <span class="text-2xl font-semibold tracking-wider text-clay">RDV<span class="text-paper">Pro</span></span>
            </div>
            <!-- Tenant info summary -->
            <div class="px-6 py-4 bg-[#211914] border-b border-[#2a201a]">
                <p class="text-sm font-bold text-paper truncate">{{ tenant('name') }}</p>
                <p class="text-xs text-muted truncate capitalize">{{ tenant('business_type') }} - Plan {{ tenant('plan') }}</p>
            </div>
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <a href="{{ route('tenant.dashboard') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.dashboard') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('tenant.appointments.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.appointments.*') && !request()->routeIs('tenant.appointments.create') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Rendez-vous
                </a>
                <a href="{{ route('tenant.customers.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.customers.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Clients (CRM)
                </a>
                <a href="{{ route('tenant.services.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.services.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Prestations
                </a>
                <a href="{{ route('tenant.resources.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.resources.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Personnel / Staff
                </a>
                <a href="{{ route('tenant.templates.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.templates.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    Modèles Messages
                </a>
                <a href="{{ route('tenant.reports.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.reports.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rapports / Perf
                </a>
                <a href="{{ route('tenant.config.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.config.*') ? 'bg-olive text-paper font-semibold' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configuration
                </a>
                <a href="{{ route('tenant.subscription.pay') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('tenant.subscription.*') ? 'bg-olive text-paper font-semibold' : 'hover:bg-[#2a201a] text-[#c5beb5]' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Mon Abonnement
                </a>
            </nav>
        </div>
        <!-- Profile / Logout -->
        <div class="p-4 border-t border-[#2a201a] flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold truncate">{{ Auth::guard('web')->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-muted">Business Owner</p>
            </div>
            <form action="{{ route('tenant.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 text-[#c5beb5] hover:text-clay rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top bar (mobile menu toggler) -->
        <header class="flex items-center justify-between h-16 px-6 bg-card border-b border-line md:hidden flex-shrink-0" x-data="{ open: false }">
            <span class="text-2xl font-semibold tracking-wider text-clay">RDV<span class="text-ink">Pro</span></span>
            <button @click="open = !open" class="p-2 rounded-lg text-ink hover:bg-paper">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Mobile Sidebar -->
            <div x-show="open" class="fixed inset-0 z-40 flex md:hidden" x-description="Off-canvas menu for mobile">
                <div class="fixed inset-0 bg-ink bg-opacity-50" @click="open = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-ink text-paper">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="open = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <svg class="h-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center h-16 px-6 bg-ink border-b border-[#2a201a]">
                        <span class="text-2xl font-semibold tracking-wider text-clay">RDV<span class="text-paper">Pro</span></span>
                    </div>
                    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                        <a href="{{ route('tenant.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('tenant.dashboard') ? 'bg-olive text-paper font-semibold' : 'hover:bg-[#2a201a] text-[#c5beb5]' }}">Tableau de bord</a>
                        <a href="{{ route('tenant.appointments.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Rendez-vous</a>
                        <a href="{{ route('tenant.customers.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Clients</a>
                        <a href="{{ route('tenant.services.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Prestations</a>
                        <a href="{{ route('tenant.resources.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Staff</a>
                        <a href="{{ route('tenant.templates.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Messages</a>
                        <a href="{{ route('tenant.reports.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Rapports</a>
                        <a href="{{ route('tenant.config.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Configuration</a>
                        <a href="{{ route('tenant.subscription.pay') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Mon Abonnement</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Dynamic Page Content -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-paper">
            <!-- Subscription Warning Banner -->
            @if(tenant() && tenant()->shouldShowSubscriptionWarning())
                <div class="mb-6 p-4 rounded-xl bg-[#fcf8f2] border border-[#e8d2be] text-clay font-semibold shadow-sm flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-clay flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-sm">
                            Votre abonnement se termine dans <strong>{{ tenant()->daysLeftForSubscription() }} jours</strong>. 
                            <a href="{{ route('tenant.subscription.pay') }}" class="underline hover:text-[#b05830] font-bold">Cliquez ici pour le renouveler</a>.
                        </span>
                    </div>
                    <button @click="show = false" class="text-clay opacity-80 hover:opacity-100">&times;</button>
                </div>
            @endif

            <div class="animate-page-entrance">
                @yield('content')
            </div>
        </main>
     </div>

    <!-- SweetAlert2 Flash Notifications -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Succès !',
                    text: "{!! addslashes(session('success')) !!}",
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3d5a3d'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Erreur !',
                    text: "{!! addslashes(session('error')) !!}",
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#c2683f'
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let htmlContent = '<ul class="text-left list-disc pl-5 mt-2 text-sm">';
                @foreach($errors->all() as $error)
                    htmlContent += '<li>{!! addslashes($error) !!}</li>';
                @endforeach
                htmlContent += '</ul>';

                Swal.fire({
                    title: 'Erreur !',
                    html: htmlContent,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#c2683f'
                });
            });
        </script>
    @endif
</body>
</html>
