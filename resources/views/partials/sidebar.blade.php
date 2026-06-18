@php
    $lien = fn ($motif) => request()->routeIs($motif)
        ? 'bg-white/10 text-white'
        : 'text-slate-400 hover:bg-white/5 hover:text-white';
@endphp
<aside class="hidden w-64 shrink-0 flex-col bg-slate-900 lg:flex">
    {{-- Marque --}}
    <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500 font-bold text-slate-900">KG</div>
        <div>
            <p class="text-sm font-semibold text-white">Koanda Groupe</p>
            <p class="text-xs text-slate-400">Système RH</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-4 text-sm font-medium">
        <p class="px-3 pb-2 pt-2 text-xs uppercase tracking-wider text-slate-500">Pilotage</p>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('dashboard') }}">
            <span class="text-base">▣</span> Tableau de bord
        </a>

        <p class="px-3 pb-2 pt-4 text-xs uppercase tracking-wider text-slate-500">Gestion</p>
        <a href="{{ route('employes.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('employes.*') }}">
            <span class="text-base">☖</span> Employés
        </a>
        <a href="{{ route('conges.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('conges.*') }}">
            <span class="text-base">▤</span> Congés
        </a>
        <a href="{{ route('filiales.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('filiales.*') }}">
            <span class="text-base">⬢</span> Filiales
        </a>
    </nav>

    <div class="border-t border-white/10 px-6 py-4">
        <p class="text-xs text-slate-500">6 filiales · Burkina Faso</p>
    </div>
</aside>
