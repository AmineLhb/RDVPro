<!DOCTYPE html>
<html lang="fr" class="h-full bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SuperAdmin Connexion - RDVPro</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
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
                        clay: '#c2683f',
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
        body { font-family: 'Outfit', sans-serif; }
        h1, h2 { font-family: 'Fraunces', serif; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full flex items-center justify-center p-6 bg-gradient-to-br from-paper via-[#FAF8F5] to-line/30 selection:bg-clay selection:text-paper">
    <div class="w-full max-w-md bg-card border border-line rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="text-center mb-8">
            <span class="text-3xl font-bold tracking-wider text-clay font-fraunces">RDV<span class="text-ink">Pro</span></span>
            <p class="mt-2 text-xs text-muted font-outfit uppercase tracking-wider font-semibold">Espace SuperAdmin Central</p>
        </div>

        <form action="{{ route('central.login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2 font-outfit">Adresse E-mail</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive/50 focus:border-transparent text-sm font-outfit transition shadow-inner">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2 font-outfit">Mot de passe</label>
                <input type="password" name="password" id="password" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive/50 focus:border-transparent text-sm font-outfit transition shadow-inner">
            </div>

            <div class="flex items-center justify-between text-xs font-semibold text-muted font-outfit">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-olive focus:ring-olive border-line rounded cursor-pointer">
                    <span class="ml-2 hover:text-ink transition">Se souvenir de moi</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-5 py-3.5 rounded-xl bg-clay text-paper font-bold hover:bg-[#b05830] transition-all duration-200 active:scale-[0.98] shadow-md hover:shadow-lg text-sm font-outfit">
                    Se connecter
                </button>
            </div>
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
