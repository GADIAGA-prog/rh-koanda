<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'RH') · Koanda Groupe</title>

    {{-- En production : @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        [x-cloak] { display: none; }
    </style>
</head>
<body class="h-full text-slate-700 antialiased">
<div class="min-h-full lg:flex">

    {{-- ===================== SIDEBAR ===================== --}}
    @include('partials.sidebar')

    {{-- ===================== CONTENU ===================== --}}
    <div class="flex-1 min-w-0">
        {{-- Barre supérieure --}}
        <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-slate-200 bg-white/90 px-6 py-3 backdrop-blur">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">@yield('rubrique', 'Tableau de bord')</p>
                <h1 class="text-lg font-semibold text-slate-900">@yield('titre', 'RH')</h1>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ auth()->user()->peutVoirToutLeGroupe() ? 'Vue Groupe' : (auth()->user()->filiale?->nom ?? 'Filiale') }}
                        </p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg px-2 py-1 text-sm text-slate-400 hover:text-slate-700" title="Déconnexion">Quitter</button>
                    </form>
                @endauth
            </div>
        </header>

        {{-- Messages flash --}}
        <div class="px-6 pt-4">
            @if (session('succes'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('succes') }}
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
