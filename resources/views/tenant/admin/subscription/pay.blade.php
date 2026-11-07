<!DOCTYPE html>
<html lang="fr" class="h-full bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RDVPro - Activation de l'abonnement</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Play Local Asset -->
    <script src="{{ global_asset('vendor/tailwindcss/tailwind.js') }}"></script>
    <script src="{{ global_asset('js/tailwind-config.js') }}"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ global_asset('css/app.css') }}">

    <!-- SweetAlert2 (JS & CSS bundle) -->
    <script src="{{ global_asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        if (window.Swal) {
            window.Swal = window.Swal.mixin({
                heightAuto: false
            });
        }
    </script>
    <style>
        html.swal2-height-auto, html.swal2-shown, body.swal2-shown {
            height: 100% !important;
        }
    </style>
</head>
<body class="h-full text-ink flex items-center justify-center p-6 bg-paper font-sans">
    <div class="max-w-2xl w-full bg-card border border-line rounded-3xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-ink text-paper p-8 text-center relative">
            <span class="text-3xl font-semibold tracking-wider text-clay font-serif">RDV<span class="text-paper">Pro</span></span>
            <h1 class="text-xl font-bold mt-4 font-serif">Gestion de votre abonnement</h1>
            <p class="text-xs text-muted mt-1">Tenant ID: {{ $tenant->id }} ({{ $tenant->name }})</p>
            
            <form action="{{ route('tenant.logout') }}" method="POST" class="absolute top-6 right-6">
                @csrf
                <button type="submit" class="text-xs text-[#c5beb5] hover:text-clay bg-[#211914] px-3 py-1.5 rounded-lg border border-[#2a201a] font-semibold transition">
                    Déconnexion
                </button>
            </form>
        </div>

        <div class="p-8 space-y-6">

            <!-- Subscription Status Widget -->
            <div class="p-5 rounded-2xl border bg-paper/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-bold text-muted uppercase tracking-wider">Statut actuel</h3>
                    <p class="text-lg font-bold text-ink mt-0.5">
                        @if($tenant->isSubscriptionActive())
                            Actif
                        @else
                            Expiré / Inactif
                        @endif
                    </p>
                    <p class="text-xs text-muted mt-1">
                        @if($tenant->getSubscriptionExpirationDate())
                            Échéance : {{ $tenant->getSubscriptionExpirationDate()->format('d/m/Y') }}
                        @else
                            Aucun abonnement actif trouvé.
                        @endif
                    </p>
                </div>
                <div>
                    @if($tenant->receipt_status === 'pending')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            Reçu en cours de vérification
                        </span>
                    @elseif($tenant->receipt_status === 'approved')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Paiement validé
                        </span>
                    @elseif($tenant->receipt_status === 'rejected')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                            Dernier reçu rejeté
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                            En attente de paiement
                        </span>
                    @endif
                </div>
            </div>

            <!-- If trial or subscription is expired, prompt message -->
            @if(!$tenant->isSubscriptionActive())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    ⚠️ <strong>Accès limité :</strong> Votre période d'essai ou votre abonnement a expiré. Veuillez choisir une offre et téléverser votre reçu de paiement pour réactiver l'accès complet à votre compte.
                </div>
            @endif

            <!-- Main Form -->
            <form action="{{ route('tenant.subscription.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Plans Selector -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-ink">1. Choisissez votre plan d'abonnement</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- 1 Month -->
                        <label class="relative flex flex-col p-4 bg-card border border-line rounded-2xl cursor-pointer hover:border-clay transition focus-within:ring-2 focus-within:ring-clay">
                            <input type="radio" name="plan" value="1_month" class="sr-only" required {{ old('plan', $tenant->selected_plan) === '1_month' ? 'checked' : '' }}>
                            <span class="block text-sm font-bold text-ink text-center">1 Mois</span>
                            <span class="block text-2xl font-black text-clay text-center mt-2">500 DH</span>
                            <span class="block text-[10px] text-muted text-center mt-1">Payable chaque mois</span>
                        </label>

                        <!-- 3 Months -->
                        <label class="relative flex flex-col p-4 bg-card border border-line rounded-2xl cursor-pointer hover:border-clay transition focus-within:ring-2 focus-within:ring-clay">
                            <input type="radio" name="plan" value="3_months" class="sr-only" {{ old('plan', $tenant->selected_plan) === '3_months' ? 'checked' : '' }}>
                            <span class="absolute -top-2.5 right-4 px-2 py-0.5 bg-olive text-paper text-[9px] font-black rounded-full uppercase tracking-wider">Save 10%</span>
                            <span class="block text-sm font-bold text-ink text-center">3 Mois</span>
                            <span class="block text-2xl font-black text-clay text-center mt-2">1350 DH</span>
                            <span class="block text-[10px] text-muted text-center mt-1">450 DH / mois</span>
                        </label>

                        <!-- 1 Year -->
                        <label class="relative flex flex-col p-4 bg-card border border-line rounded-2xl cursor-pointer hover:border-clay transition focus-within:ring-2 focus-within:ring-clay">
                            <input type="radio" name="plan" value="1_year" class="sr-only" {{ old('plan', $tenant->selected_plan) === '1_year' ? 'checked' : '' }}>
                            <span class="absolute -top-2.5 right-4 px-2 py-0.5 bg-clay text-paper text-[9px] font-black rounded-full uppercase tracking-wider">Save 30%</span>
                            <span class="block text-sm font-bold text-ink text-center">1 An</span>
                            <span class="block text-2xl font-black text-clay text-center mt-2">4200 DH</span>
                            <span class="block text-[10px] text-muted text-center mt-1">350 DH / mois</span>
                        </label>
                    </div>
                </div>

                <!-- Payment details -->
                <div class="p-5 rounded-2xl bg-ink/5 border border-line space-y-2">
                    <h4 class="text-xs font-bold text-ink uppercase tracking-wider">Coordonnées bancaires pour le virement</h4>
                    <div class="text-sm space-y-1 text-ink/80">
                        <p><strong>Banque :</strong> CIH Bank</p>
                        <p><strong>Bénéficiaire :</strong> RDVPro SARL</p>
                        <p><strong>RIB :</strong> 011 223 3445566778899001 22</p>
                        <p class="text-[11px] text-muted mt-1">Veuillez indiquer le nom de votre sous-domaine (<strong>{{ $tenant->id }}</strong>) dans le motif du virement.</p>
                    </div>
                </div>

                <!-- Receipt Upload -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-ink">2. Téléversez le reçu de paiement</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-line border-dashed rounded-2xl hover:border-clay transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-muted" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h20a4 4 0 004-4V20m-6-12l-6-6m6 6l6 6m-9-6v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-ink justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-card rounded-md font-bold text-clay hover:underline focus-within:outline-none">
                                    <span>Téléverser un fichier</span>
                                    <input id="file-upload" name="receipt" type="file" class="sr-only" required>
                                </label>
                                <p class="pl-1 text-muted">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-muted">Formats acceptés : PDF, PNG, JPG, JPEG jusqu'à 5 Mo</p>
                            <p id="file-name" class="text-xs font-semibold text-olive mt-2"></p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between gap-4 pt-4">
                    @if($tenant->isSubscriptionActive())
                        <a href="{{ route('tenant.dashboard') }}" class="px-5 py-3 rounded-xl border border-line hover:bg-paper font-semibold transition text-sm">
                            ← Retour au tableau de bord
                        </a>
                    @else
                        <div></div>
                    @endif
                    <button type="submit" class="px-6 py-3 rounded-xl bg-clay text-paper font-bold hover:bg-[#b05830] transition shadow-md text-sm">
                        Envoyer le reçu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dynamic file name rendering -->
    <script>
        document.getElementById('file-upload').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('file-name').textContent = fileName ? 'Fichier sélectionné : ' + fileName : '';
        });
    </script>

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

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let htmlContent = '<ul class="text-left list-disc pl-5 mt-2 text-sm">';
                @foreach($errors->all() as $error)
                    htmlContent += '<li>{{ addslashes($error) }}</li>';
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
