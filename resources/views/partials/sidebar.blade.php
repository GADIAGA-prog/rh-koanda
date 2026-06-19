@php
    $navClass = fn ($motif) => request()->routeIs($motif)
        ? 'bg-koanda-light text-forest font-semibold shadow-sm'
        : 'text-slate-300 hover:bg-white/5 hover:text-white';
    $iconClass = fn ($motif) => request()->routeIs($motif)
        ? 'text-koanda-dark'
        : 'text-slate-400 group-hover:text-koanda';
@endphp
<aside class="hidden w-64 shrink-0 flex-col bg-forest lg:flex">
    {{-- Marque --}}
    <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-koanda font-display text-base font-extrabold text-forest">K</div>
        <div>
            <p class="font-display text-sm font-bold text-white">KOANDA GROUPE</p>
            <p class="text-xs text-slate-400">Système RH</p>
        </div>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4 text-sm font-medium">
        {{-- Pilotage --}}
        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('dashboard') }}">
            <x-icon name="home" class="h-5 w-5 {{ $iconClass('dashboard') }}" /> Tableau de bord
        </a>

        {{-- Gestion du personnel --}}
        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Gestion du personnel</p>
        <a href="{{ route('employes.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('employes.*') }}">
            <x-icon name="users" class="h-5 w-5 {{ $iconClass('employes.*') }}" /> Employés
        </a>
        @can('contrat.view')
            <a href="{{ route('contrats.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('contrats.*') }}">
                <x-icon name="document" class="h-5 w-5 {{ $iconClass('contrats.*') }}" /> Contrats
            </a>
        @endcan
        @can('organisation.view')
            <a href="{{ route('departements.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('departements.*') }}">
                <x-icon name="building" class="h-5 w-5 {{ $iconClass('departements.*') }}" /> Départements
            </a>
            <a href="{{ route('postes.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('postes.*') }}">
                <x-icon name="briefcase" class="h-5 w-5 {{ $iconClass('postes.*') }}" /> Postes
            </a>
            <a href="{{ route('fonctions.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('fonctions.*') }}">
                <x-icon name="identification" class="h-5 w-5 {{ $iconClass('fonctions.*') }}" /> Fonctions
            </a>
            <a href="{{ route('organigramme') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('organigramme') }}">
                <x-icon name="sitemap" class="h-5 w-5 {{ $iconClass('organigramme') }}" /> Organigramme
            </a>
        @endcan
        @can('sanction.view')
            <a href="{{ route('sanctions.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('sanctions.*') }}">
                <x-icon name="gavel" class="h-5 w-5 {{ $iconClass('sanctions.*') }}" /> Discipline
            </a>
        @endcan

        {{-- Paie & avantages --}}
        @can('paie.view')
            <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Paie &amp; avantages</p>
            <a href="{{ route('paie.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('paie.*') }}">
                <x-icon name="banknote" class="h-5 w-5 {{ $iconClass('paie.*') }}" /> Salaires
            </a>
            <a href="{{ route('rubriques.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('rubriques.*') }}">
                <x-icon name="banknote" class="h-5 w-5 {{ $iconClass('rubriques.*') }}" /> Rubriques de paie
            </a>
        @endcan

        {{-- Présence & absences --}}
        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Présence &amp; absences</p>
        @can('presence.view')
            <a href="{{ route('presences.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('presences.*') }}">
                <x-icon name="calendar" class="h-5 w-5 {{ $iconClass('presences.*') }}" /> Pointages
            </a>
        @endcan
        <a href="{{ route('conges.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('conges.*') }}">
            <x-icon name="calendar" class="h-5 w-5 {{ $iconClass('conges.*') }}" /> Congés
        </a>
        @can('document.view')
            <a href="{{ route('documents.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('documents.*') }}">
                <x-icon name="document" class="h-5 w-5 {{ $iconClass('documents.*') }}" /> Documents RH
            </a>
        @endcan
        @can('absence.view')
            <a href="{{ route('absences.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('absences.*') }}">
                <x-icon name="calendar" class="h-5 w-5 {{ $iconClass('absences.*') }}" /> Absences
            </a>
        @endcan
        @can('mission.view')
            <a href="{{ route('missions.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('missions.*') }}">
                <x-icon name="map" class="h-5 w-5 {{ $iconClass('missions.*') }}" /> Missions
            </a>
        @endcan

        {{-- Performance & formation --}}
        @canany(['performance.view', 'formation.view'])
            <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Performance &amp; formation</p>
            @can('performance.view')
                <a href="{{ route('evaluations.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('evaluations.*') }}">
                    <x-icon name="star" class="h-5 w-5 {{ $iconClass('evaluations.*') }}" /> Évaluations
                </a>
            @endcan
            @can('formation.view')
                <a href="{{ route('formations.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('formations.*') }}">
                    <x-icon name="academic" class="h-5 w-5 {{ $iconClass('formations.*') }}" /> Formations
                </a>
            @endcan
        @endcanany

        {{-- Rapports --}}
        @can('rapport.consulter')
            <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Rapports</p>
            <a href="{{ route('rapports.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('rapports.*') }}">
                <x-icon name="chart" class="h-5 w-5 {{ $iconClass('rapports.*') }}" /> Rapports RH
            </a>
        @endcan

        {{-- Administration --}}
        @can('utilisateur.view')
            <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Administration</p>
            <a href="{{ route('filiales.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('filiales.*') }}">
                <x-icon name="building" class="h-5 w-5 {{ $iconClass('filiales.*') }}" /> Filiales
            </a>
            <a href="{{ route('admin.utilisateurs.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('admin.utilisateurs.*') }}">
                <x-icon name="user" class="h-5 w-5 {{ $iconClass('admin.utilisateurs.*') }}" /> Utilisateurs
            </a>
            @can('role.manage')
                <a href="{{ route('admin.roles.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('admin.roles.*') }}">
                    <x-icon name="shield" class="h-5 w-5 {{ $iconClass('admin.roles.*') }}" /> Rôles &amp; permissions
                </a>
            @endcan
        @endcan

        {{-- Compte --}}
        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold uppercase tracking-wider text-koanda/70">Compte</p>
        <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ $navClass('profile.*') }}">
            <x-icon name="cog" class="h-5 w-5 {{ $iconClass('profile.*') }}" /> Mon profil
        </a>
    </nav>

    <div class="border-t border-white/10 px-6 py-4">
        <p class="text-xs text-slate-500">6 filiales · Burkina Faso</p>
    </div>
</aside>
