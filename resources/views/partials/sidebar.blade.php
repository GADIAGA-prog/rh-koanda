@php
    $lien = fn ($motif) => request()->routeIs($motif)
        ? 'bg-koanda/15 text-white'
        : 'text-slate-400 hover:bg-white/5 hover:text-white';
    $puce = fn ($motif) => request()->routeIs($motif) ? 'bg-koanda' : 'bg-transparent';
@endphp
<aside class="hidden w-64 shrink-0 flex-col bg-forest lg:flex">
    {{-- Marque --}}
    <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-koanda font-display text-base font-extrabold text-forest">KG</div>
        <div>
            <p class="font-display text-sm font-bold text-white">Koanda Groupe</p>
            <p class="text-xs text-slate-400">Système RH</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-4 text-sm font-medium">
        <p class="px-3 pb-2 pt-2 text-xs uppercase tracking-wider text-slate-500">Pilotage</p>
        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('dashboard') }}">
            <span class="h-5 w-1 rounded-full {{ $puce('dashboard') }}"></span>Tableau de bord
        </a>

        <p class="px-3 pb-2 pt-4 text-xs uppercase tracking-wider text-slate-500">Gestion</p>
        <a href="{{ route('employes.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('employes.*') }}">
            <span class="h-5 w-1 rounded-full {{ $puce('employes.*') }}"></span>Employés
        </a>
        <a href="{{ route('conges.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('conges.*') }}">
            <span class="h-5 w-1 rounded-full {{ $puce('conges.*') }}"></span>Congés
        </a>
        <a href="{{ route('filiales.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('filiales.*') }}">
            <span class="h-5 w-1 rounded-full {{ $puce('filiales.*') }}"></span>Filiales
        </a>

        @can('utilisateur.view')
            <p class="px-3 pb-2 pt-4 text-xs uppercase tracking-wider text-slate-500">Administration</p>
            <a href="{{ route('admin.utilisateurs.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('admin.utilisateurs.*') }}">
                <span class="h-5 w-1 rounded-full {{ $puce('admin.utilisateurs.*') }}"></span>Utilisateurs
            </a>
            @can('role.manage')
                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('admin.roles.*') }}">
                    <span class="h-5 w-1 rounded-full {{ $puce('admin.roles.*') }}"></span>Rôles &amp; permissions
                </a>
            @endcan
        @endcan

        <p class="px-3 pb-2 pt-4 text-xs uppercase tracking-wider text-slate-500">Compte</p>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $lien('profile.*') }}">
            <span class="h-5 w-1 rounded-full {{ $puce('profile.*') }}"></span>Mon profil
        </a>
    </nav>

    <div class="border-t border-white/10 px-6 py-4">
        <p class="text-xs text-slate-500">6 filiales · Burkina Faso</p>
    </div>
</aside>
