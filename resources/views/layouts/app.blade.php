<!DOCTYPE html>
<html lang="fr" class="h-full bg-mineral">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'RH') · Koanda Groupe</title>

    {{-- En production : @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Charte graphique Groupe Koanda (couleurs + typographie)
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        koanda: { DEFAULT: '#70B339', dark: '#5D9730', light: '#E9F4DF' },
                        forest: { DEFAULT: '#07120B', soft: '#0F1F15' },
                        mineral: '#F4F5F2',
                        mist: '#DDE1DA',
                        slatetext: '#46514A',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Montserrat', 'Inter', 'ui-sans-serif', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        h1, h2, .font-display { font-family: 'Montserrat', 'Inter', sans-serif; }
        [x-cloak] { display: none; }
    </style>
</head>
<body class="h-full text-slatetext antialiased">
<div class="min-h-full lg:flex">

    {{-- ===================== SIDEBAR ===================== --}}
    @include('partials.sidebar')

    {{-- ===================== CONTENU ===================== --}}
    <div class="flex-1 min-w-0">
        {{-- Barre supérieure --}}
        <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-mist bg-white/90 px-6 py-3 backdrop-blur">
            <div>
                <h1 class="font-display text-xl font-bold text-forest">@yield('titre', 'RH')</h1>
                {{-- Fil d'Ariane : Accueil › rubrique(s) › titre --}}
                <nav class="mt-0.5 flex flex-wrap items-center gap-1 text-xs text-slate-400">
                    <a href="{{ route('dashboard') }}" class="hover:text-koanda-dark">Accueil</a>
                    @foreach (array_filter(array_map('trim', explode('·', View::yieldContent('rubrique')))) as $crumb)
                        <x-icon name="chevron" class="h-3 w-3 text-mist" />
                        <span>{{ $crumb }}</span>
                    @endforeach
                    <x-icon name="chevron" class="h-3 w-3 text-mist" />
                    <span class="font-medium text-slatetext">@yield('titre', 'RH')</span>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    @php
                        $nonLues = auth()->user()->unreadNotifications()->count();
                        $roleNom = auth()->user()->roles->first()?->name;
                        $roleLibelle = $roleNom ? (\App\Models\User::ROLES_META[$roleNom][0] ?? $roleNom) : null;
                    @endphp
                    {{-- Notifications --}}
                    <div class="relative">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-mineral hover:text-forest" title="Notifications">
                            <x-icon name="bell" class="h-5 w-5" />
                        </span>
                        @if ($nonLues > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $nonLues > 9 ? '9+' : $nonLues }}</span>
                        @endif
                    </div>

                    {{-- Utilisateur --}}
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-koanda text-sm font-semibold text-white">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden leading-tight sm:block">
                            <p class="text-sm font-semibold text-forest">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400">{{ $roleLibelle ?? (auth()->user()->peutVoirToutLeGroupe() ? 'Vue Groupe' : (auth()->user()->filiale?->nom ?? 'Filiale')) }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-mist px-3 py-1.5 text-sm text-slate-500 transition hover:border-rose-200 hover:text-rose-600" title="Déconnexion">Quitter</button>
                    </form>
                @endauth
            </div>
        </header>

        {{-- Messages flash --}}
        <div class="px-6 pt-4">
            @if (session('succes'))
                <div class="mb-4 flex items-center gap-2 rounded-lg border border-koanda/30 bg-koanda-light px-4 py-3 text-sm font-medium text-koanda-dark">
                    <span class="text-base leading-none">✓</span> {{ session('succes') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-medium">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
                    </ul>
                </div>
            @endif
        </div>

        <main class="px-6 py-6">
            @yield('contenu')
        </main>
    </div>
</div>
</body>
</html>
