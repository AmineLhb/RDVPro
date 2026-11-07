<!DOCTYPE html>
<html lang="fr" class="h-full bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RDVPro Central Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#1a1410',
                        paper: '#f7f3ec',
                        card: '#fffdf9',
                        olive: '#3d5a3d',
                        'olive-2': '#52734f',
                        clay: '#c2683f',
                        'clay-soft': '#e8a47b',
                        gold: '#d4a017',
                        line: '#e4ddd0',
                        muted: '#8a8273',
                    },
                    fontFamily: {
                        fraunces: ['Fraunces', 'serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-serif {
            font-family: 'Fraunces', serif;
        }
        html.swal2-height-auto, html.swal2-shown, body.swal2-shown {
            height: 100% !important;
        }
    </style>

    <!-- Custom CSS (extracted inline styles & animations) -->
    <link rel="stylesheet" href="{{ global_asset('css/app.css') }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (window.Swal) {
            window.Swal = window.Swal.mixin({
                heightAuto: false
            });
        }

        function confirmActionCentral(button, options = {}) {
            Swal.fire({
                title: options.title || 'Êtes-vous sûr ?',
                text: options.text || '',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: options.confirmColor || '#3d5a3d', // olive
                cancelButtonColor: options.cancelColor || '#8a8273', // muted
                confirmButtonText: options.confirmText || 'Oui',
                cancelButtonText: options.cancelText || 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = button.closest('form');
                    if (form) form.submit();
                }
            });
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-ink flex overflow-hidden">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:flex-shrink-0 bg-ink text-paper w-64 flex-col justify-between">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Brand -->
            <div class="flex items-center h-16 px-6 bg-ink border-b border-[#2a201a]">
                <span class="text-2xl font-semibold tracking-wider text-clay">RDV<span class="text-paper">Pro</span></span>
                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-clay text-paper">Central</span>
            </div>
            <!-- Navigation -->
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('central.dashboard') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('central.dashboard') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('central.tenants.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('central.tenants.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Clients (Tenants)
                </a>
                <a href="{{ route('central.domains.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('central.domains.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    Domaines
                </a>
                <a href="{{ route('central.exports.index') }}" class="group flex items-center px-3 py-3 rounded-xl transition duration-300 ease-out transform active:scale-98 hover:translate-x-1 {{ request()->routeIs('central.exports.*') ? 'bg-olive text-paper font-semibold border-l-4 border-clay pl-2' : 'hover:bg-[#2a201a] text-[#c5beb5] border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 transition duration-300 ease-out group-hover:scale-110 group-hover:text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Reçus
                </a>
            </nav>
        </div>
        <!-- SuperAdmin Profile -->
        <div class="p-4 border-t border-[#2a201a] flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold">{{ Auth::guard('superadmin')->user()->name ?? 'SuperAdmin' }}</p>
                <p class="text-xs text-muted">SuperAdmin</p>
            </div>
            <form action="{{ route('central.logout') }}" method="POST">
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
                        <a href="{{ route('central.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl bg-olive text-paper">Dashboard</a>
                        <a href="{{ route('central.tenants.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Clients</a>
                        <a href="{{ route('central.domains.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Domaines</a>
                        <a href="{{ route('central.exports.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-[#2a201a] text-[#c5beb5]">Export Reçus</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Dynamic Page Content -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-paper">
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
