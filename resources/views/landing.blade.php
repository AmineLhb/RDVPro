<!DOCTYPE html>
<html lang="fr" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RDVPro - Gestion de Rendez-vous & CRM Premium</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Play Local Asset -->
    <script src="{{ global_asset('vendor/tailwindcss/tailwind.js') }}?v=1.0.0"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#16110d',
                        'ink-2': '#221a14',
                        paper: '#f7f3ec',
                        card: '#fffdf9',
                        olive: '#3d5a3d',
                        'olive-2': '#6f9268',
                        clay: '#c2683f',
                        'clay-soft': '#e8a47b',
                        gold: '#d4a017',
                        muted: '#9a9183',
                    },
                    fontFamily: {
                        serif: ['Fraunces', 'serif'],
                        sans: ['Outfit', 'sans-serif'],
                    },
                    borderRadius: {
                        'brand-sm': '11px',
                        'brand-md': '18px',
                        'brand-lg': '28px',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #16110d;
            color: #f7f3ec;
        }

        h1, h2, h3, h4, h5, h6, .font-serif {
            font-family: 'Fraunces', serif;
        }

        /* Animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* 3D floating animation */
        @keyframes float {
            0%, 100% {
                transform: rotateY(-13deg) rotateX(6deg) rotateZ(1deg) translateY(0);
            }
            50% {
                transform: rotateY(-13deg) rotateX(6deg) rotateZ(1deg) translateY(-14px);
            }
        }

        @keyframes float-flat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .perspective-container {
            perspective: 1000px;
        }

        .calendar-card-3d {
            transform-style: preserve-3d;
            transform: rotateY(-13deg) rotateX(6deg) rotateZ(1deg);
            animation: float 7s ease-in-out infinite;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .calendar-card-3d:hover {
            transform: rotateY(-4deg) rotateX(2deg) rotateZ(0deg) scale(1.02);
            animation-play-state: paused;
        }

        /* Bobbing chips */
        @keyframes bob-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes bob-fast {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-bob-slow {
            animation: bob-slow 5s ease-in-out infinite;
        }

        .animate-bob-fast {
            animation: bob-fast 4s ease-in-out infinite;
        }

        /* Pop in staggered */
        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.85);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-pop-in {
            opacity: 0;
            animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Sweep timeline */
        @keyframes sweep {
            0% {
                top: 15%;
                opacity: 0;
            }
            10%, 90% {
                opacity: 1;
            }
            100% {
                top: 90%;
                opacity: 0;
            }
        }

        .animate-sweep-line {
            animation: sweep 7s linear infinite;
        }

        /* Scrolling marquee */
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-marquee {
            animation: marquee 26s linear infinite;
        }

        /* Feature cards reveal classes */
        .reveal-card {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Keyboard focus rings */
        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid #c2683f;
            outline-offset: 4px;
        }

        /* Mobile overrides */
        @media (max-width: 768px) {
            .calendar-card-3d {
                transform: none !important;
                animation: float-flat 7s ease-in-out infinite !important;
            }
        }

        /* Reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-delay: -1ms !important;
                animation-duration: 1ms !important;
                animation-iteration-count: 1 !important;
                background-attachment: initial !important;
                scroll-behavior: auto !important;
                transition-duration: 0s !important;
                transition-delay: 0s !important;
            }
            .animate-calendar-float, .animate-pop-in, .animate-sweep-line, .animate-bob-slow, .animate-bob-fast, .animate-marquee, .animate-fade-up, .calendar-card-3d {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .reveal-card {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="bg-ink text-paper selection:bg-clay selection:text-paper overflow-x-hidden min-h-full flex flex-col justify-between">

    <!-- 1. Fixed Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-ink/75 backdrop-blur-xl border-b border-paper/5 transition duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            
            <!-- Logo -->
            <a href="#" class="flex items-center space-x-2.5 group">
                <div class="w-9 h-9 rounded-brand-sm bg-gradient-to-tr from-clay to-gold flex items-center justify-center shadow-lg shadow-clay/10 transition-transform group-hover:scale-105">
                    <span class="font-serif font-black text-xl text-ink leading-none">R</span>
                </div>
                <span class="text-xl font-bold tracking-wider text-paper font-sans">RDV<span class="text-clay-soft">Pro</span></span>
            </a>

            <!-- Text Links (Hidden on Mobile) -->
            <div class="hidden md:flex items-center space-x-8 font-sans">
                <a href="#features" class="text-sm font-semibold hover:text-clay-soft transition-all relative group py-2">
                    Fonctionnalités
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-clay transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#why-us" class="text-sm font-semibold hover:text-clay-soft transition-all relative group py-2">
                    Pourquoi nous
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-clay transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#pricing" class="text-sm font-semibold hover:text-clay-soft transition-all relative group py-2">
                    Tarifs
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-clay transition-all duration-300 group-hover:w-full"></span>
                </a>
            </div>

            <!-- CTA Buttons -->
            <div class="flex items-center space-x-4 font-sans">
                <button onclick="openModal()" class="text-sm font-semibold px-4 py-2.5 rounded-brand-sm border border-transparent hover:bg-paper/5 text-paper transition-all duration-200 active:scale-95">
                    Se connecter
                </button>
                <button onclick="openRegisterModal()" class="px-5 py-2.5 rounded-brand-sm bg-clay text-paper font-bold hover:bg-clay-soft transition-all duration-200 active:scale-95 shadow-md shadow-clay/10 hover:shadow-lg text-sm">
                    Commencer gratuit
                </button>
            </div>
        </div>
    </nav>

    <!-- 2. Hero Section -->
    <main class="relative min-h-screen flex items-center justify-center overflow-hidden pt-24 pb-16">
        
        <!-- Background Glow Blobs -->
        <div class="absolute top-1/4 right-1/12 w-[35vw] h-[35vw] max-w-[500px] bg-clay/10 blur-[130px] rounded-full z-0"></div>
        <div class="absolute bottom-1/4 left-1/12 w-[35vw] h-[35vw] max-w-[500px] bg-olive/10 blur-[130px] rounded-full z-0"></div>

        <div class="max-w-7xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center relative z-10">
            
            <!-- Left Column: Marketing Copy -->
            <div class="lg:col-span-6 space-y-8 text-center lg:text-left">
                
                <!-- Eyebrow -->
                <div class="inline-flex items-center px-4 py-1.5 rounded-full text-[11px] font-bold bg-ink-2 text-clay-soft border border-paper/5 uppercase tracking-widest animate-fade-up" style="animation-delay: 0s;">
                    <span class="w-2 h-2 rounded-full bg-olive-2 mr-2.5 animate-pulse"></span>
                    Rendez-vous · Calendrier · CRM Clients
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-6xl font-semibold font-serif leading-[1.08] tracking-[-2px] text-paper animate-fade-up" style="animation-delay: 0.13s;">
                    Your whole schedule,<br>
                    <span class="text-clay-soft italic font-normal font-serif">handled.</span>
                </h1>

                <!-- Lead Paragraph -->
                <p class="text-base sm:text-lg text-muted max-w-lg mx-auto lg:mx-0 leading-relaxed font-sans font-medium animate-fade-up" style="animation-delay: 0.26s;">
                    RDVPro turns the daily chaos of bookings, reminders, and no-shows into one calm calendar. Built for clinics, salons, and anyone who runs on appointments — replacing the paper diary for good.
                </p>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2 animate-fade-up" style="animation-delay: 0.39s;">
                    <button onclick="openRegisterModal()" class="w-full sm:w-auto px-8 py-4 bg-clay text-paper font-bold rounded-brand-md hover:bg-clay-soft transition-all shadow-lg shadow-clay/10 text-sm text-center">
                        Commencer gratuit ➔
                    </button>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-ink-2/60 border border-paper/10 backdrop-blur-md text-paper font-bold rounded-brand-md hover:bg-paper/5 transition-all text-sm text-center">
                        Découvrir les fonctionnalités
                    </a>
                </div>

                <!-- Trust Row -->
                <div class="pt-4 text-xs font-semibold text-muted/80 flex items-center justify-center lg:justify-start gap-1.5 animate-fade-up" style="animation-delay: 0.52s;">
                    <span>✓ Sans carte bancaire</span>
                    <span class="text-clay-soft/40 font-bold">·</span>
                    <span>Configuration en 2 min</span>
                    <span class="text-clay-soft/40 font-bold">·</span>
                    <span>Annulation à tout moment</span>
                </div>
            </div>

            <!-- Right Column: Signature Animated Calendar -->
            <div class="lg:col-span-6 flex items-center justify-center perspective-container relative pt-12 lg:pt-0">
                
                <!-- Floating Chip 1 (Top Left) -->
                <div class="absolute -top-4 left-0 sm:left-4 z-40 bg-[#fffdf9]/95 backdrop-blur-md text-ink p-3 rounded-brand-sm border border-paper/15 shadow-xl flex items-center space-x-3 animate-bob-slow hidden md:flex transform -rotate-2">
                    <div class="w-8 h-8 rounded-lg bg-olive/15 text-olive flex items-center justify-center text-sm font-bold">✓</div>
                    <div class="text-left">
                        <p class="text-[11px] font-bold tracking-tight text-ink/90 leading-none">Reminder sent</p>
                        <p class="text-[9px] font-medium text-muted mt-0.5">WhatsApp · 24h before</p>
                    </div>
                </div>

                <!-- Calendar Card -->
                <div class="w-full max-w-[440px] bg-card rounded-brand-lg border border-paper/10 shadow-[0_24px_50px_-12px_rgba(0,0,0,0.5)] p-5 text-ink calendar-card-3d relative overflow-hidden select-none">
                    
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between border-b border-paper/40 pb-4 mb-4">
                        <div class="text-left">
                            <h3 class="font-serif font-black text-xl tracking-tight text-[#221a14]">This week</h3>
                            <p class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">8 appointments today</p>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-4 gap-2.5 relative">
                        
                        <!-- Sweeping "Now" Line -->
                        <div class="absolute left-0 right-0 h-[2px] bg-clay/80 z-30 animate-sweep-line pointer-events-none">
                            <div class="w-2.5 h-2.5 rounded-full bg-clay absolute -left-1 -top-[4px] shadow-[0_0_10px_#c2683f]"></div>
                        </div>

                        <!-- Time Column -->
                        <div class="col-span-1 space-y-8 pr-2 border-r border-paper/30 text-right">
                            <div class="text-[10px] font-bold text-muted uppercase tracking-wider">Time</div>
                            <div class="text-xs font-serif font-bold text-[#221a14] h-10 flex items-center justify-end">09:00</div>
                            <div class="text-xs font-serif font-bold text-[#221a14] h-10 flex items-center justify-end">10:00</div>
                            <div class="text-xs font-serif font-bold text-[#221a14] h-10 flex items-center justify-end">11:00</div>
                            <div class="text-xs font-serif font-bold text-[#221a14] h-10 flex items-center justify-end">12:00</div>
                        </div>

                        <!-- Days Columns -->
                        <div class="col-span-3 grid grid-cols-3 gap-2 text-center relative min-h-[200px]">
                            <!-- Days Titles -->
                            <div class="col-span-3 grid grid-cols-3 gap-2 mb-2">
                                <div class="text-[10px] font-bold text-muted uppercase tracking-wider">Mon</div>
                                <div class="text-[10px] font-bold text-muted uppercase tracking-wider">Tue</div>
                                <div class="text-[10px] font-bold text-muted uppercase tracking-wider">Wed</div>
                            </div>

                            <!-- Column Areas -->
                            <!-- Monday Column -->
                            <div class="space-y-2 relative">
                                <!-- Appt 1 (Mon 09:00): Youssef / Check-up -->
                                <div class="p-2 bg-olive text-paper rounded-brand-sm text-[10px] leading-tight text-left shadow-sm font-semibold animate-pop-in" style="animation-delay: 0.9s;">
                                    <p class="font-bold truncate">Youssef</p>
                                    <p class="text-[9px] opacity-90 truncate font-normal">Check-up</p>
                                </div>
                                <!-- Appt 2 (Mon 10:30): Salma / Cleaning -->
                                <div class="p-2 bg-clay text-paper rounded-brand-sm text-[10px] leading-tight text-left shadow-sm font-semibold animate-pop-in" style="animation-delay: 1.3s;">
                                    <p class="font-bold truncate">Salma</p>
                                    <p class="text-[9px] opacity-90 truncate font-normal">Cleaning</p>
                                </div>
                            </div>

                            <!-- Tuesday Column -->
                            <div class="space-y-2 relative pt-6">
                                <!-- Appt 3 (Tue 09:30): Sara / Consult -->
                                <div class="p-2 bg-gold text-ink rounded-brand-sm text-[10px] leading-tight text-left shadow-sm font-semibold animate-pop-in" style="animation-delay: 1.7s;">
                                    <p class="font-bold truncate">Sara</p>
                                    <p class="text-[9px] opacity-90 truncate font-normal">Consult</p>
                                </div>
                                <!-- Appt 4 (Tue 11:00): Omar / Follow-up -->
                                <div class="p-2 bg-olive text-paper rounded-brand-sm text-[10px] leading-tight text-left shadow-sm font-semibold animate-pop-in" style="animation-delay: 2.1s;">
                                    <p class="font-bold truncate">Omar</p>
                                    <p class="text-[9px] opacity-90 truncate font-normal">Follow-up</p>
                                </div>
                            </div>

                            <!-- Wednesday Column -->
                            <div class="space-y-2 relative pt-12">
                                <!-- Appt 5 (Wed 10:00): Lina / Check-up -->
                                <div class="p-2 bg-clay text-paper rounded-brand-sm text-[10px] leading-tight text-left shadow-sm font-semibold animate-pop-in" style="animation-delay: 2.5s;">
                                    <p class="font-bold truncate">Lina</p>
                                    <p class="text-[9px] opacity-90 truncate font-normal">Check-up</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Chip 2 (Bottom Right) -->
                <div class="absolute -bottom-2 right-0 sm:right-4 z-40 bg-[#fffdf9]/95 backdrop-blur-md text-ink p-3 rounded-brand-sm border border-paper/15 shadow-xl flex items-center space-x-3 animate-bob-fast hidden md:flex transform rotate-2">
                    <div class="w-8 h-8 rounded-lg bg-clay/15 text-clay flex items-center justify-center text-sm font-bold">↑</div>
                    <div class="text-left">
                        <p class="text-[11px] font-bold tracking-tight text-ink/90 leading-none">No-shows down 64%</p>
                        <p class="text-[9px] font-medium text-muted mt-0.5">this month</p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- 3. Marquee Strip -->
    <section class="w-full bg-ink-2 py-5 border-y border-paper/5 overflow-hidden relative z-20">
        <div class="flex whitespace-nowrap overflow-hidden">
            <div class="flex space-x-12 items-center animate-marquee select-none">
                <!-- Set of Marquee Text -->
                <span class="text-base sm:text-lg font-serif font-medium text-muted tracking-wide flex items-center">
                    Bookings <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reminders <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Calendar <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Clients <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    WhatsApp <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Staff <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Exports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    No-shows gone
                </span>
                <!-- Duplicate 1 -->
                <span class="text-base sm:text-lg font-serif font-medium text-muted tracking-wide flex items-center">
                    Bookings <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reminders <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Calendar <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Clients <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    WhatsApp <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Staff <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Exports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    No-shows gone
                </span>
                <!-- Duplicate 2 -->
                <span class="text-base sm:text-lg font-serif font-medium text-muted tracking-wide flex items-center">
                    Bookings <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reminders <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Calendar <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Clients <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Reports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    WhatsApp <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Staff <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    Exports <span class="mx-6 text-clay-soft font-sans font-bold">·</span>
                    No-shows gone
                </span>
            </div>
        </div>
    </section>

    <!-- 4. Features Section -->
    <section id="features" class="max-w-7xl mx-auto px-6 py-24 sm:py-32 relative z-20">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
            <span id="why-us" class="text-xs font-bold text-clay uppercase tracking-widest font-sans">Everything in one place</span>
            <h2 class="text-3xl sm:text-5xl font-semibold font-serif text-paper leading-tight tracking-[-1.5px]">
                Run the whole day from one screen
            </h2>
            <p class="text-base text-muted font-sans font-medium">
                The calendar is just the beginning. RDVPro automates your client interactions, saves hours on admin, and keeps your chair full.
            </p>
        </div>

        <!-- 3x2 Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Card 1 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-clay/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-clay transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-clay/10 text-clay flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Smart calendar</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">See every appointment across all staff at a glance. Drag to reschedule, tap to book. Double-bookings are blocked automatically.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-olive/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-olive transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-olive/15 text-olive-2 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Auto reminders</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">WhatsApp, SMS, and email reminders go out on their own — before every appointment. Watch no-shows fall.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-gold/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-gold transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-gold/15 text-gold flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Client records</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">Every client, their history, visits, and notes in one searchable place. Labels for VIPs, new, and at-risk.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-clay/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-clay transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-clay/10 text-clay flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Reports & exports</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">Revenue, busiest hours, top services, staff performance — with one-click PDF and Excel exports.</p>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-olive/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-olive transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-olive/15 text-olive-2 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Configure anything</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">Your services, your staff, your hours, your rules. The platform shapes itself to how you actually work.</p>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="reveal-card bg-[#1c1815] border-t-2 border-t-gold/60 border-x border-b border-[#2d2520] rounded-brand-lg p-8 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:border-gold transition-all duration-300 ease-out group select-none flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-brand-sm bg-gold/15 text-gold flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold font-serif text-paper">Works anywhere</h3>
                    <p class="text-sm text-muted leading-relaxed font-sans font-medium">Laptop at the desk, phone on the go. Your whole schedule travels with you — nothing to install.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- 4.5. Why Us Section -->
    <section id="why-us" class="max-w-7xl mx-auto px-6 py-24 border-t border-paper/5 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Column: Copy -->
            <div class="lg:col-span-5 space-y-6">
                <span class="text-xs font-bold text-clay uppercase tracking-widest font-sans">Pourquoi nous</span>
                <h2 class="text-3xl sm:text-5xl font-semibold font-serif text-paper leading-tight tracking-[-1.5px]">
                    Conçu pour la croissance, pas pour l'administration
                </h2>
                <p class="text-base text-muted leading-relaxed font-sans font-medium">
                    Contrairement aux agendas génériques qui se contentent de bloquer des créneaux, RDVPro est pensé de bout en bout pour les professionnels dont le temps est précieux.
                </p>
                <div class="pt-2">
                    <button onclick="openRegisterModal()" class="px-6 py-3.5 bg-clay text-paper font-bold rounded-brand-md hover:bg-clay-soft hover:-translate-y-0.5 active:scale-95 duration-200 transform transition-all text-sm shadow-md">
                        Découvrir la différence ➔
                    </button>
                </div>
            </div>

            <!-- Right Column: 3 Pillar Cards -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Pillar 1 -->
                <div class="reveal-card bg-[#1c1815] border border-[#2d2520] rounded-brand-md p-6 hover:bg-paper/5 hover:border-olive transition duration-300 flex items-start space-x-5 group">
                    <div class="w-10 h-10 rounded-brand-sm bg-olive/10 text-olive flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5 text-olive" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-bold font-serif text-paper">Cabinet, Salon, Gym — sur mesure</h4>
                        <p class="text-xs text-muted leading-relaxed font-sans font-medium">La plateforme s'adapte à votre terminologie. Patients, clients, séances ou traitements — RDVPro s'intègre naturellement à votre vocabulaire de travail quotidien.</p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="reveal-card bg-[#1c1815] border border-[#2d2520] rounded-brand-md p-6 hover:bg-paper/5 hover:border-clay transition duration-300 flex items-start space-x-5 group">
                    <div class="w-10 h-10 rounded-brand-sm bg-clay/10 text-clay flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5 text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-bold font-serif text-paper">Le WhatsApp que vos clients adorent</h4>
                        <p class="text-xs text-muted leading-relaxed font-sans font-medium">Les rappels partent automatiquement sur WhatsApp depuis un canal professionnel. Taux de lecture de 98%, confirmations instantanées et zéro no-show.</p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="reveal-card bg-[#1c1815] border border-[#2d2520] rounded-brand-md p-6 hover:bg-paper/5 hover:border-gold transition duration-300 flex items-start space-x-5 group">
                    <div class="w-10 h-10 rounded-brand-sm bg-gold/10 text-gold flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-bold font-serif text-paper">Gardez 100% de vos gains</h4>
                        <p class="text-xs text-muted leading-relaxed font-sans font-medium">Pas de mauvaise surprise ni de frais de réservation masqués pour vos clients. Vous payez un abonnement fixe, tout est transparent.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 5. Stats Band Section -->
    <section class="w-full bg-ink-2 py-20 border-y border-paper/5 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center">
                
                <!-- Stat 1 -->
                <div class="space-y-2">
                    <h4 class="text-5xl sm:text-6xl font-serif font-semibold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gold to-clay-soft" id="stat-no-shows">0%</h4>
                    <p class="text-xs sm:text-sm font-semibold text-muted uppercase tracking-widest mt-1">fewer no-shows</p>
                </div>

                <!-- Stat 2 -->
                <div class="space-y-2">
                    <h4 class="text-5xl sm:text-6xl font-serif font-semibold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gold to-clay-soft" id="stat-setup">0</h4>
                    <p class="text-xs sm:text-sm font-semibold text-muted uppercase tracking-widest mt-1">minutes to set up</p>
                </div>

                <!-- Stat 3 -->
                <div class="space-y-2">
                    <h4 class="text-5xl sm:text-6xl font-serif font-semibold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gold to-clay-soft" id="stat-hours">0</h4>
                    <p class="text-xs sm:text-sm font-semibold text-muted uppercase tracking-widest mt-1">hours saved weekly</p>
                </div>

                <!-- Stat 4 -->
                <div class="space-y-2">
                    <h4 class="text-5xl sm:text-6xl font-serif font-semibold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gold to-clay-soft" id="stat-commission">0%</h4>
                    <p class="text-xs sm:text-sm font-semibold text-muted uppercase tracking-widest mt-1">yours, no commission</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 5.5. Pricing Section -->
    <section id="pricing" class="max-w-7xl mx-auto px-6 py-24 sm:py-32 relative z-20 border-t border-paper/5">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
            <span class="text-xs font-bold text-clay uppercase tracking-widest font-sans">Nos Tarifs</span>
            <h2 class="text-3xl sm:text-5xl font-semibold font-serif text-paper leading-tight tracking-[-1.5px]">
                Des tarifs clairs, adaptés à vos besoins
            </h2>
            <p class="text-base text-muted font-sans font-medium">
                Commencez avec un essai gratuit de 30 jours. Aucune carte bancaire requise.
            </p>
        </div>

        <!-- 3-Column Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            
            <!-- Plan 1: Monthly -->
            <div class="reveal-card bg-ink-2/30 border border-paper/5 rounded-brand-lg p-8 flex flex-col justify-between hover:border-paper/10 transition duration-300 relative">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold font-serif text-paper">Plan Mensuel</h3>
                        <p class="text-xs text-muted">Parfait pour démarrer sereinement.</p>
                    </div>
                    <div class="flex items-baseline space-x-1">
                        <span class="text-4xl sm:text-5xl font-serif font-black text-paper">500 DH</span>
                        <span class="text-xs text-muted font-semibold">/ mois</span>
                    </div>
                    <p class="text-xs text-muted">Facturé mensuellement. Sans engagement.</p>
                    <ul class="space-y-3 pt-4 border-t border-paper/5 text-xs text-muted font-semibold">
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Calendrier complet & collaboratif</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Relances WhatsApp automatiques</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Base CRM clients & historique</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Exports PDF et Excel en 1 clic</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> 30 jours d'essai gratuit</li>
                    </ul>
                </div>
                <div class="pt-8">
                    <button onclick="openRegisterModal()" class="w-full py-3 border border-paper/10 rounded-brand-sm text-xs font-bold hover:bg-paper/5 transition duration-300">
                        Essayer gratuitement
                    </button>
                </div>
            </div>

            <!-- Plan 2: Quarterly -->
            <div class="reveal-card bg-[#fffdf9]/5 border-2 border-clay rounded-brand-lg p-8 flex flex-col justify-between hover:shadow-xl transition duration-300 relative">
                <!-- Popular Badge -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-clay text-paper text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Recommandé (Économisez 10%)
                </div>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold font-serif text-paper">Plan Trimestriel</h3>
                        <p class="text-xs text-clay-soft font-bold">Le meilleur compromis pour grandir.</p>
                    </div>
                    <div class="flex items-baseline space-x-1">
                        <span class="text-4xl sm:text-5xl font-serif font-black text-paper">1350 DH</span>
                        <span class="text-xs text-muted font-semibold">/ 3 mois</span>
                    </div>
                    <p class="text-xs text-clay-soft/80 font-bold">Soit 450 DH / mois. Économisez 150 DH.</p>
                    <ul class="space-y-3 pt-4 border-t border-clay/20 text-xs text-muted font-semibold">
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Tout ce qui est inclus dans le Mensuel</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Support technique prioritaire</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Session d'aide à l'import de vos clients</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Personnalisation avancée de l'agenda</li>
                    </ul>
                </div>
                <div class="pt-8">
                    <button onclick="openRegisterModal()" class="w-full py-3 bg-clay text-paper rounded-brand-sm text-xs font-bold hover:bg-clay-soft transition duration-300 shadow-md shadow-clay/10">
                        Essayer gratuitement
                    </button>
                </div>
            </div>

            <!-- Plan 3: Annual -->
            <div class="reveal-card bg-ink-2/30 border border-paper/5 rounded-brand-lg p-8 flex flex-col justify-between hover:border-paper/10 transition duration-300 relative">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold font-serif text-paper">Plan Annuel</h3>
                        <p class="text-xs text-muted">Tranquillité et économie maximale.</p>
                    </div>
                    <div class="flex items-baseline space-x-1">
                        <span class="text-4xl sm:text-5xl font-serif font-black text-paper">4200 DH</span>
                        <span class="text-xs text-muted font-semibold">/ an</span>
                    </div>
                    <p class="text-xs text-muted">Soit 350 DH / mois. Économisez 1800 DH !</p>
                    <ul class="space-y-3 pt-4 border-t border-paper/5 text-xs text-muted font-semibold">
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Tout ce qui est inclus dans le Trimestriel</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Accès anticipé aux nouvelles fonctions</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Support VIP 24/7 en ligne & WhatsApp</li>
                        <li class="flex items-center"><span class="text-olive mr-2">✓</span> Configuration complète clé en main par nos soins</li>
                    </ul>
                </div>
                <div class="pt-8">
                    <button onclick="openRegisterModal()" class="w-full py-3 border border-paper/10 rounded-brand-sm text-xs font-bold hover:bg-paper/5 transition duration-300">
                        Essayer gratuitement
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- 6. Closing CTA Section -->
    <section id="pricing-cta" class="relative max-w-5xl mx-auto px-6 py-28 flex items-center justify-center overflow-hidden z-20">
        
        <!-- Radial Clay Glow behind card -->
        <div class="absolute inset-0 flex items-center justify-center z-0">
            <div class="w-[60%] h-[60%] bg-clay/20 blur-[130px] rounded-full"></div>
        </div>

        <!-- Card content -->
        <div class="relative z-10 w-full bg-card text-ink rounded-brand-lg border border-paper/10 shadow-2xl p-10 sm:p-16 text-center space-y-8 max-w-3xl">
            <div class="space-y-3">
                <h2 class="text-3xl sm:text-5xl font-serif font-black text-[#221a14] tracking-tight">
                    Your calendar is waiting.
                </h2>
                <p class="text-base text-muted max-w-lg mx-auto font-medium">
                    Set up your practice in two minutes. No card, no commitment — just a calmer day.
                </p>
            </div>

            <div class="pt-2">
                <button onclick="openRegisterModal()" class="w-full sm:w-auto px-8 py-4 bg-clay text-paper font-bold rounded-brand-md hover:bg-clay-soft transition-all shadow-lg shadow-clay/10 text-sm text-center">
                    Start free today ➔
                </button>
            </div>
        </div>
    </section>

    <!-- 7. Footer -->
    <footer class="bg-ink border-t border-paper/5 py-16 relative z-20">
        <div class="max-w-7xl mx-auto px-6 text-center space-y-6">
            
            <!-- Logo -->
            <div class="flex items-center justify-center space-x-2.5">
                <div class="w-8 h-8 rounded-brand-sm bg-gradient-to-tr from-clay to-gold flex items-center justify-center shadow-md shadow-clay/10">
                    <span class="font-serif font-black text-lg text-ink leading-none">R</span>
                </div>
                <span class="text-lg font-bold tracking-wider text-paper font-sans">RDV<span class="text-clay-soft">Pro</span></span>
            </div>

            <!-- Tagline -->
            <p class="text-sm font-serif italic text-muted max-w-md mx-auto">
                Appointments, reminders, and clients — handled.
            </p>

            <!-- Copyright -->
            <div class="text-xs text-muted/60 pt-4 font-semibold font-sans">
                © {{ date('Y') }} RDVPro Inc. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Workspace Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/70 backdrop-blur-md opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-[#fffdf9] text-ink p-8 rounded-brand-lg border border-paper/10 shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300 space-y-6" id="loginModalCard">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-serif font-black text-2xl tracking-tight text-[#221a14]">Accéder à votre espace</h3>
                    <p class="text-xs text-muted font-medium mt-1">Saisissez l'identifiant de votre commerce pour vous connecter.</p>
                </div>
                <button onclick="closeModal()" class="text-muted hover:text-ink text-xl font-bold font-sans">&times;</button>
            </div>

            <form onsubmit="redirectToWorkspace(event)" class="space-y-4">
                <div>
                    <label for="workspaceName" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Workspace ID</label>
                    <div class="flex rounded-brand-sm border border-paper/40 bg-[#fffdf9] overflow-hidden focus-within:ring-2 focus-within:ring-clay">
                        <input type="text" id="workspaceName" placeholder="nom-de-votre-commerce" required class="flex-1 px-4 py-3 bg-[#fffdf9] text-ink focus:outline-none text-sm font-semibold">
                        <span class="bg-paper px-4 py-3 text-muted text-sm font-semibold flex items-center border-l border-paper/40">.rdvpro.test:8000</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-clay text-paper font-bold rounded-brand-sm hover:bg-clay-soft transition-all text-sm shadow-md shadow-clay/10">
                    Accéder à mon espace ➔
                </button>
            </form>
        </div>
    </div>

    <!-- Workspace Register / Commercial Modal -->
    <div id="registerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/70 backdrop-blur-md opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-[#fffdf9] text-ink p-8 rounded-brand-lg border border-paper/10 shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300 space-y-6" id="registerModalCard">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-serif font-black text-2xl tracking-tight text-[#221a14]">Créer votre espace</h3>
                    <p class="text-xs text-muted font-medium mt-1">Démarrez votre essai gratuit dès aujourd'hui.</p>
                </div>
                <button onclick="closeRegisterModal()" class="text-muted hover:text-ink text-xl font-bold font-sans">&times;</button>
            </div>

            <div class="space-y-4 text-sm leading-relaxed text-[#221a14]">
                <p>Pour des raisons de sécurité et pour configurer au mieux vos horaires et vos ressources, la création d'espaces est actuellement assistée par notre équipe technique.</p>
                <div class="p-4 rounded-brand-sm bg-paper/60 border border-paper/40 space-y-2">
                    <p class="font-bold text-clay">📞 Contact Direct :</p>
                    <p class="font-semibold text-xs text-muted">WhatsApp / Tél : <span class="text-ink font-bold">+212 600 000 000</span></p>
                    <p class="font-semibold text-xs text-muted">Email : <span class="text-ink font-bold">contact@rdvpro.ma</span></p>
                </div>
                <p class="text-xs text-muted">Votre espace sera opérationnel en moins de 2 minutes chronomètre en main.</p>
            </div>

            <button onclick="closeRegisterModal()" class="w-full py-3 bg-[#221a14] text-paper font-bold rounded-brand-sm hover:bg-ink transition-all text-sm">
                Fermer
            </button>
        </div>
    </div>

    <!-- JavaScript Actions & Animations -->
    <script>
        // Modal Handlers
        const loginModal = document.getElementById('loginModal');
        const loginModalCard = document.getElementById('loginModalCard');
        const registerModal = document.getElementById('registerModal');
        const registerModalCard = document.getElementById('registerModalCard');

        function openModal() {
            loginModal.classList.remove('opacity-0', 'pointer-events-none');
            loginModalCard.classList.remove('scale-95');
            loginModalCard.classList.add('scale-100');
            document.getElementById('workspaceName').focus();
        }

        function closeModal() {
            loginModal.classList.add('opacity-0', 'pointer-events-none');
            loginModalCard.classList.remove('scale-100');
            loginModalCard.classList.add('scale-95');
        }

        function openRegisterModal() {
            registerModal.classList.remove('opacity-0', 'pointer-events-none');
            registerModalCard.classList.remove('scale-95');
            registerModalCard.classList.add('scale-100');
        }

        function closeRegisterModal() {
            registerModal.classList.add('opacity-0', 'pointer-events-none');
            registerModalCard.classList.remove('scale-100');
            registerModalCard.classList.add('scale-95');
        }

        // Close on clicking backdrop
        window.addEventListener('click', (e) => {
            if (e.target === loginModal) closeModal();
            if (e.target === registerModal) closeRegisterModal();
        });

        // Redirect logic to tenant workspace
        function redirectToWorkspace(event) {
            event.preventDefault();
            const workspace = document.getElementById('workspaceName').value.trim().toLowerCase();
            if (!workspace) return;

            // Extract main domain (e.g. rdvpro.test:8000 or rdvpro.test or domain.com)
            const currentHost = window.location.host; // e.g. "rdvpro.test:8000" or "127.0.0.1:8000"
            
            // Clean host - remove any existing tenant subdomains if present (should not be, but safe fallback)
            let baseHost = currentHost;
            if (currentHost.includes('rdvpro.test')) {
                // If it contains a subdomain, extract rdvpro.test:8000
                const parts = currentHost.split('.');
                if (parts.length > 2) {
                    baseHost = parts.slice(-2).join('.');
                }
            }

            // Redirect
            window.location.href = `http://${workspace}.${baseHost}/login`;
        }

        // Intersection Observer for scroll-reveal
        document.addEventListener('DOMContentLoaded', () => {
            // Animation preferences check
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            if (!prefersReducedMotion && 'IntersectionObserver' in window) {
                const revealCards = document.querySelectorAll('.reveal-card');
                
                const observerOptions = {
                    root: null,
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Add visibility class
                            entry.target.classList.add('visible');
                            
                            // Optional staggering offset inside column layout
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);

                revealCards.forEach(card => {
                    observer.observe(card);
                });

                // Stats band count up triggering
                const statsSection = document.getElementById('stat-no-shows').parentElement.parentElement;
                
                const statsObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            triggerStatsCountUp();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.2 });

                statsObserver.observe(statsSection);
            } else {
                // Reduced motion active or IntersectionObserver not supported, set final states directly
                document.querySelectorAll('.reveal-card').forEach(card => {
                    card.classList.add('visible');
                });
                document.getElementById('stat-no-shows').textContent = '64%';
                document.getElementById('stat-setup').textContent = '2';
                document.getElementById('stat-hours').textContent = '8';
                document.getElementById('stat-commission').textContent = '100%';
            }
        });

        // Stats Count Up Implementation
        function triggerStatsCountUp() {
            animateCount('stat-no-shows', 64, '%');
            animateCount('stat-setup', 2, '');
            animateCount('stat-hours', 8, '');
            animateCount('stat-commission', 100, '%');
        }

        function animateCount(id, target, suffix) {
            const el = document.getElementById(id);
            if (!el) return;
            
            let start = 0;
            const duration = 1500; // 1.5s
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const current = Math.floor(progress * (target - start) + start);
                el.textContent = current + suffix;
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.textContent = target + suffix;
                }
            }
            window.requestAnimationFrame(step);
        }
    </script>
</body>
</html>
