@extends('tenant.layout')

@section('content')
<div class="relative min-h-[75vh] w-full rounded-3xl overflow-hidden shadow-lg border border-line/40 flex items-center justify-center">
    
    <!-- Background Video Loop inside content card -->
    <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0 opacity-40 mix-blend-overlay">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-glowing-particles-background-loop-42861-large.mp4" type="video/mp4">
    </video>
    
    <!-- Ambient Canvas Fallback -->
    <canvas id="particlesCanvas" class="absolute inset-0 z-0 bg-[#1a1410] opacity-95"></canvas>
    
    <!-- Dark glassmorphism overlay container -->
    <div class="absolute inset-0 bg-gradient-to-tr from-[#1a1410]/95 via-[#1a1410]/75 to-transparent z-10"></div>
    
    <!-- Main Welcome Text and animations -->
    <div class="relative z-20 max-w-2xl text-center px-6 py-12 text-paper space-y-8">
        <div class="space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-clay/30 text-clay-soft border border-clay-soft/20 uppercase tracking-widest animate-pulse">
                👋 Bienvenue sur votre espace
            </span>
            <h1 class="text-3xl sm:text-5xl font-black font-serif text-paper leading-tight tracking-wide">
                {{ tenant('name') }}
            </h1>
            <p class="text-sm text-clay-soft font-serif italic tracking-wide text-lg">
                La solution complète pour la gestion de votre activité.
            </p>
        </div>
        
        <p class="text-sm text-muted/90 max-w-md mx-auto leading-relaxed">
            RDVPro vous permet d'optimiser vos plannings, de suivre vos performances financières et de communiquer automatiquement avec vos clients par SMS et WhatsApp.
        </p>

        <!-- Slider/Grid of Features explaining the platform -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl mx-auto pt-4 text-left">
            <div class="group p-5 bg-paper/5 border border-paper/10 backdrop-blur-md rounded-2xl space-y-3 hover:bg-paper/10 hover:border-paper/20 hover:-translate-y-0.5 transition-all duration-300">
                <div class="p-2 bg-paper/5 rounded-xl inline-block">
                    <svg class="w-5 h-5 text-clay-soft group-hover:scale-110 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-clay-soft font-outfit">Tableau de bord</h3>
                <p class="text-[11px] text-[#c5beb5] font-outfit leading-relaxed">Suivi en temps réel des revenus cumulés, rendez-vous du jour et taux d'absence.</p>
            </div>
            <div class="group p-5 bg-paper/5 border border-paper/10 backdrop-blur-md rounded-2xl space-y-3 hover:bg-paper/10 hover:border-paper/20 hover:-translate-y-0.5 transition-all duration-300">
                <div class="p-2 bg-paper/5 rounded-xl inline-block">
                    <svg class="w-5 h-5 text-clay-soft group-hover:scale-110 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-clay-soft font-outfit">Planning & Calendrier</h3>
                <p class="text-[11px] text-[#c5beb5] font-outfit leading-relaxed">Gestion visuelle de vos rendez-vous, glissez-déposez des créneaux pour vos clients.</p>
            </div>
            <div class="group p-5 bg-paper/5 border border-paper/10 backdrop-blur-md rounded-2xl space-y-3 hover:bg-paper/10 hover:border-paper/20 hover:-translate-y-0.5 transition-all duration-300">
                <div class="p-2 bg-paper/5 rounded-xl inline-block">
                    <svg class="w-5 h-5 text-clay-soft group-hover:scale-110 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-clay-soft font-outfit">Messages WhatsApp</h3>
                <p class="text-[11px] text-[#c5beb5] font-outfit leading-relaxed">Envoi automatique de confirmations et de rappels 24h avant chaque rendez-vous.</p>
            </div>
            <div class="group p-5 bg-paper/5 border border-paper/10 backdrop-blur-md rounded-2xl space-y-3 hover:bg-paper/10 hover:border-paper/20 hover:-translate-y-0.5 transition-all duration-300">
                <div class="p-2 bg-paper/5 rounded-xl inline-block">
                    <svg class="w-5 h-5 text-clay-soft group-hover:scale-110 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-clay-soft font-outfit">Configuration</h3>
                <p class="text-[11px] text-[#c5beb5] font-outfit leading-relaxed">Personnalisation de vos horaires de travail, collaborateurs et catalogue de prestations.</p>
            </div>
        </div>

        <div class="pt-6">
            <a href="{{ route('tenant.dashboard') }}" class="inline-flex items-center px-8 py-3.5 bg-clay text-paper font-bold rounded-2xl hover:bg-[#b05830] transition shadow-lg text-sm tracking-wide">
                Accéder au Tableau de Bord ➔
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('particlesCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        let width = canvas.width = canvas.parentElement.offsetWidth;
        let height = canvas.height = canvas.parentElement.offsetHeight;
        
        window.addEventListener('resize', () => {
            if (canvas && canvas.parentElement) {
                width = canvas.width = canvas.parentElement.offsetWidth;
                height = canvas.height = canvas.parentElement.offsetHeight;
            }
        });
        
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.radius = Math.random() * 2 + 0.5;
                this.speedX = Math.random() * 0.3 - 0.15;
                this.speedY = Math.random() * -0.2 - 0.05; // float up
                this.opacity = Math.random() * 0.4 + 0.1;
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
                ctx.fillStyle = `rgba(194, 104, 63, ${this.opacity})`; // clay particles
                ctx.fill();
            }
        }
        
        const particlesArray = [];
        for (let i = 0; i < 40; i++) {
            particlesArray.push(new Particle());
        }
        
        function animate() {
            ctx.clearRect(0, 0, width, height);
            
            // Draw dark background color
            ctx.fillStyle = '#1a1410';
            ctx.fillRect(0, 0, width, height);
            
            particlesArray.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animate);
        }
        animate();
    });
</script>
@endsection
