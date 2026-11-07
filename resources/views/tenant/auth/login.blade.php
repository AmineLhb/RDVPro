<!DOCTYPE html>
<html lang="fr" class="h-full bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion Administrateur - {{ tenant('name') }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script src="{{ global_asset('vendor/tailwindcss/tailwind.js') }}"></script>
    <script src="{{ global_asset('js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ global_asset('css/app.css') }}">
    <script src="{{ global_asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
</head>
<body class="h-full flex items-center justify-center p-6 bg-paper">
    <div class="w-full max-w-md bg-card border border-line rounded-2xl p-8 shadow-sm">
        <div class="text-center mb-8">
            <span class="text-3xl font-bold tracking-wider text-clay">RDV<span class="text-ink">Pro</span></span>
            <h2 class="text-xl font-semibold text-ink mt-3">{{ tenant('name') }}</h2>
            <p class="text-xs text-muted capitalize">{{ tenant('business_type') }} - Administration</p>
        </div>

        <form action="{{ route('tenant.login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-ink mb-2">Adresse E-mail</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive focus:border-transparent text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-ink mb-2">Mot de passe</label>
                <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive focus:border-transparent text-sm">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                    <span class="ml-2 text-muted font-semibold">Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-clay text-paper font-semibold text-sm hover:bg-[#b05830] transition shadow-sm">
                Se connecter
            </button>
        </form>
    </div>

    <!-- SweetAlert2 Error Notification -->
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Erreur !',
                    text: "{!! addslashes($errors->first()) !!}",
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#c2683f'
                });
            });
        </script>
    @endif
</body>
</html>
