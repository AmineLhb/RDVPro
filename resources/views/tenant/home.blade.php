<!DOCTYPE html>
<html lang="fr" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ tenant('name') }} - RDVPro</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Play Local Asset -->
    <script src="{{ global_asset('vendor/tailwindcss/tailwind.js') }}"></script>
    <script src="{{ global_asset('js/tailwind-config.js') }}"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ global_asset('css/app.css') }}">

    <!-- Alpine.js Local Asset -->
    <script defer src="{{ global_asset('vendor/alpinejs/alpine.min.js') }}"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-serif {
            font-family: 'Fraunces', serif;
        }
        /* Custom animated gradient fallback */
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-bg {
            background: linear-gradient(-45deg, #1a1410, #2a201a, #3d5a3d, #253325);
            background-size: 400% 400%;
            animation: gradientFlow 15s ease infinite;
        }
    </style>
</head>
<body class="bg-paper text-ink selection:bg-clay selection:text-paper overflow-x-hidden min-h-full flex flex-col justify-between">

    @php
        $businessType = tenant('business_type');
        $category = 'Prestations';
        $headline = 'Prenez vos rendez-vous en toute simplicité.';
        $description = 'Planifiez vos séances en ligne et gérez vos créneaux en temps réel.';
        
        switch ($businessType) {
            case 'clinic':
                $category = 'Santé & Soins';
                $headline = 'Votre santé, notre priorité. Planifiez votre consultation.';
                $description = 'Notre clinique met à votre disposition un agenda intelligent pour réserver vos soins avec nos médecins spécialistes.';
                break;
            case 'gym':
                $category = 'Sport & Fitness';
                $headline = 'Dépassez vos limites. Réservez vos séances.';
                $description = 'Planifiez vos séances d\'entraînement individuel ou collectif et suivez vos progrès avec nos coachs certifiés.';
                break;
            case 'salon':
            default:
                $category = 'Beauté & Bien-être';
                $headline = 'Sublimez votre style. Prenez rendez-vous en ligne.';
                $description = 'Notre salon vous accueille pour des prestations sur mesure. Réservez votre créneau avec votre professionnel préféré.';
                break;
        }
    @endphp

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-paper/60 backdrop-blur-lg border-b border-line/40 transition duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-2">
                <span class="text-2xl font-bold tracking-wider text-clay font-serif">RDV<span class="text-ink">Pro</span></span>
                <span class="text-xs px-2 py-0.5 rounded bg-ink/5 text-muted font-semibold capitalize">{{ tenant('business_type') }}</span>
            </a>
            
            <div class="flex items-center space-x-6">
                <a href="#services" class="text-sm font-semibold hover:text-clay transition">Services</a>
                <a href="#staff" class="text-sm font-semibold hover:text-clay transition">Équipe</a>
                <a href="{{ route('tenant.login') }}" class="px-5 py-2.5 rounded-xl bg-ink text-paper font-semibold hover:bg-clay hover:text-paper transition shadow-sm text-sm">
                    Espace Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section with Background Video & Canvas Fallback -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden pt-20">
        <!-- Canvas Background (glowing animated particles) -->
        <canvas id="particlesCanvas" class="absolute inset-0 z-0 animated-bg"></canvas>

        <!-- Background Video (HTML5 Loop) -->
        <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0 opacity-40 mix-blend-overlay">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-glowing-particles-background-loop-42861-large.mp4" type="video/mp4">
        </video>

        <!-- Dark Tint Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-paper via-transparent to-transparent z-10"></div>

        <!-- Hero Content -->
        <div class="relative z-20 max-w-4xl mx-auto px-6 text-center space-y-8 text-paper mt-12">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-clay/20 text-clay-soft border border-clay/30 uppercase tracking-widest animate-pulse">
                ✨ {{ $category }}
            </span>
            <h1 class="text-4xl sm:text-6xl font-black font-serif leading-tight text-paper drop-shadow-sm">
                {{ tenant('name') }} <br>
                <span class="text-clay-soft font-serif italic font-normal text-3xl sm:text-5xl mt-2 block">{{ $headline }}</span>
            </h1>
            <p class="text-lg text-[#ece6dc] max-w-2xl mx-auto font-medium">
                {{ $description }}
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="#services" class="w-full sm:w-auto px-8 py-4 bg-clay text-paper font-bold rounded-2xl hover:bg-[#b05830] transition shadow-lg text-sm text-center">
                    Découvrir nos services
                </a>
                <a href="{{ route('tenant.login') }}" class="w-full sm:w-auto px-8 py-4 bg-paper/10 border border-paper/20 backdrop-blur-md text-paper font-bold rounded-2xl hover:bg-paper/20 transition text-sm text-center">
                    Gérer le planning
                </a>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <div class="bg-paper relative z-30">
        
        <!-- Platform Pitch Section -->
        <section class="max-w-7xl mx-auto px-6 py-24 border-t border-line/40">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="p-8 rounded-3xl bg-card border border-line/60 shadow-sm space-y-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 bg-olive/10 text-olive rounded-2xl flex items-center justify-center text-xl font-bold">📅</div>
                    <h3 class="text-xl font-bold font-serif text-ink">Réservation Instantanée</h3>
                    <p class="text-sm text-muted">Réservez vos prestations en ligne en quelques secondes avec des confirmations en temps réel.</p>
                </div>
                <div class="p-8 rounded-3xl bg-card border border-line/60 shadow-sm space-y-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 bg-clay/10 text-clay rounded-2xl flex items-center justify-center text-xl font-bold">💬</div>
                    <h3 class="text-xl font-bold font-serif text-ink">Rappels WhatsApp & SMS</h3>
                    <p class="text-sm text-muted">Ne manquez plus aucun rendez-vous grâce à notre système de notifications automatisées.</p>
                </div>
                <div class="p-8 rounded-3xl bg-card border border-line/60 shadow-sm space-y-4 hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 bg-ink/5 text-ink rounded-2xl flex items-center justify-center text-xl font-bold">⭐</div>
                    <h3 class="text-xl font-bold font-serif text-ink">Équipe Professionnelle</h3>
                    <p class="text-sm text-muted">Choisissez votre collaborateur préféré et planifiez vos séances avec nos experts qualifiés.</p>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="max-w-7xl mx-auto px-6 py-20 border-t border-line/40">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold text-clay uppercase tracking-widest">Nos Prestations</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-ink">Choisissez le soin qui vous correspond</h2>
                <p class="text-sm text-muted">Consultez nos tarifs transparents et planifiez votre séance.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($services as $service)
                    <div class="bg-card border border-line/60 rounded-3xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex justify-between items-start gap-4">
                                <h4 class="text-lg font-bold text-ink font-serif">{{ $service->name }}</h4>
                            </div>
                            <p class="text-xs text-muted mt-2">
                                ⏱️ Durée : {{ $service->duration_minutes }} minutes
                            </p>
                        </div>
                        <div class="border-t border-line/40 pt-4 mt-6 flex items-center justify-between">
                            <span class="text-xl font-black text-clay">{{ number_format($service->price, 2) }} DH</span>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded bg-olive/10 text-olive">
                                {{ $service->requires_deposit ? 'Acompte requis' : 'Paiement sur place' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-muted">
                        <p class="text-sm font-semibold">Aucun service n'est configuré pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Staff Section -->
        <section id="staff" class="max-w-7xl mx-auto px-6 py-20 border-t border-line/40">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold text-clay uppercase tracking-widest">Notre Équipe</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-ink">Rencontrez nos professionnels</h2>
                <p class="text-sm text-muted">Des experts passionnés par leur métier, à votre service.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
                @forelse($resources as $resource)
                    <div class="bg-card border border-line/60 rounded-3xl p-8 shadow-sm flex flex-col items-center text-center hover:border-olive/40 transition">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl text-paper font-black font-serif shadow-inner" style="background-color: {{ $resource->color ?? '#3d5a3d' }}">
                            {{ substr($resource->name, 0, 1) }}
                        </div>
                        <h4 class="text-xl font-bold text-ink mt-6 font-serif">{{ $resource->name }}</h4>
                        <p class="text-xs text-clay font-bold uppercase tracking-wider mt-1">{{ $resource->role ?? 'Collaborateur' }}</p>
                        
                        <div class="mt-6 pt-4 border-t border-line/40 w-full">
                            <p class="text-[11px] text-muted font-semibold uppercase tracking-wider">Services assurés</p>
                            <div class="flex flex-wrap justify-center gap-1.5 mt-2">
                                @forelse($resource->services as $srv)
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-ink/5 font-semibold text-ink/80">{{ $srv->name }}</span>
                                @empty
                                    <span class="text-[10px] text-muted italic">Tous services</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-muted">
                        <p class="text-sm font-semibold">Aucun collaborateur n'est configuré pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-ink text-paper py-12 border-t border-[#2a201a]">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-1">
                <span class="text-xl font-bold tracking-wider text-clay font-serif">RDV<span class="text-paper">Pro</span></span>
                <p class="text-xs text-muted">© {{ date('Y') }} {{ tenant('name') }}. Tous droits réservés.</p>
            </div>
            
            <div class="flex gap-6 text-xs text-[#c5beb5] font-semibold">
                <a href="{{ route('tenant.login') }}" class="hover:text-clay transition">Administration</a>
                <span class="text-muted">|</span>
                <p>Propulsé par RDVPro Central</p>
            </div>
        </div>
    </footer>

    <!-- Interactive Canvas Particles Script -->
    <script>
        const canvas = document.getElementById('particlesCanvas');
        const ctx = canvas.getContext('2d');
        
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;
        
        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });
        
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.radius = Math.random() * 2.5 + 0.5;
                this.speedX = Math.random() * 0.4 - 0.2;
                this.speedY = Math.random() * -0.3 - 0.1; // rise upwards
                this.opacity = Math.random() * 0.5 + 0.2;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.y < 0) {
                    this.y = height;
                    this.x = Math.random() * width;
                }
                if (this.x < 0 || this.x > width) {
                    this.speedX *= -1;
                }
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(194, 104, 63, ${this.opacity})`; // Clay particles
                ctx.fill();
            }
        }
        
        const particlesArray = [];
        for (let i = 0; i < 60; i++) {
            particlesArray.push(new Particle());
        }
        
        function animate() {
            ctx.clearRect(0, 0, width, height);
            
            // Draw background flow animation via script to enhance depth
            ctx.fillStyle = 'rgba(26, 20, 16, 0.95)'; // dark ambient backdrop
            ctx.fillRect(0, 0, width, height);
            
            particlesArray.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>
</html>
