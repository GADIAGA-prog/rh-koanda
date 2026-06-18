@extends('layouts.app')
@section('titre', 'Utilisateurs')
@section('rubrique', 'Administration')

@section('contenu')
{{-- En-tête + mini statistiques --}}
<div class="flex flex-wrap items-end justify-between gap-4">
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-mist bg-white px-5 py-3 shadow-sm">
            <p class="font-display text-2xl font-bold tracking-tight text-forest">{{ $stats['total'] }}</p>
            <p class="text-xs font-medium text-slate-400">Comptes</p>
        </div>
        <div class="rounded-xl border border-mist bg-white px-5 py-3 shadow-sm">
            <p class="font-display text-2xl font-bold tracking-tight text-koanda-dark">{{ $stats['actifs'] }}</p>
            <p class="text-xs font-medium text-slate-400">Actifs</p>
        </div>
        <div class="rounded-xl border border-mist bg-white px-5 py-3 shadow-sm">
            <p class="font-display text-2xl font-bold tracking-tight text-slatetext">{{ $stats['inactifs'] }}</p>
            <p class="text-xs font-medium text-slate-400">Désactivés</p>
        </div>
    </div>
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.utilisateurs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-koanda px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">
            <span class="text-base leading-none">+</span> Nouvel utilisateur
        </a>
    @endcan
</div>

{{-- Filtres --}}
<form method="GET" class="mt-5 grid grid-cols-1 gap-3 rounded-xl border border-mist bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
    <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Rechercher un nom ou email…"
           class="rounded-lg border-mist text-sm focus:border-koanda focus:ring-koanda">
    <select name="role" class="rounded-lg border-mist text-sm focus:border-koanda focus:ring-koanda">
        <option value="">Tous les rôles</option>
        @foreach ($roles as $r)
            <option value="{{ $r }}" @selected(($filtres['role'] ?? '') === $r)>{{ \App\Models\User::ROLES_META[$r][0] ?? $r }}</option>
        @endforeach
    </select>
    <select name="actif" class="rounded-lg border-mist text-sm focus:border-koanda focus:ring-koanda">
        <option value="">Tous les statuts</option>
        <option value="1" @selected(($filtres['actif'] ?? '') === '1')>Actifs</option>
        <option value="0" @selected(($filtres['actif'] ?? '') === '0')>Désactivés</option>
    </select>
    <div class="flex gap-2">
        <button class="flex-1 rounded-lg bg-forest px-4 py-2 text-sm font-medium text-white transition hover:bg-forest-soft">Filtrer</button>
        <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-mist px-4 py-2 text-sm text-slatetext transition hover:bg-mineral">Réinit.</a>
    </div>
</form>

{{-- Tableau --}}
<div class="mt-4 overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-5 py-3 font-semibold">Utilisateur</th>
                <th class="px-5 py-3 font-semibold">Rôle</th>
                <th class="px-5 py-3 font-semibold">Périmètre</th>
                <th class="px-5 py-3 font-semibold">Statut</th>
                <th class="px-5 py-3 text-right font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist">
            @forelse ($utilisateurs as $u)
                @php
                    $mots = preg_split('/\s+/', trim($u->name));
                    $initiales = strtoupper(mb_substr($mots[0] ?? '', 0, 1) . (count($mots) > 1 ? mb_substr(end($mots), 0, 1) : ''));
                    $gereesSupp = $u->filialesGerees->count();
                @endphp
                <tr class="transition hover:bg-koanda-light/40">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-koanda text-xs font-semibold text-white">{{ $initiales ?: '?' }}</div>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-forest">{{ $u->name }}</p>
                                <p class="truncate text-xs text-slate-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3"><x-role-badge :role="$u->roles->pluck('name')->first()" /></td>
                    <td class="px-5 py-3 text-slatetext">
                        {{ $u->filiale->nom ?? 'Groupe' }}
                        @if ($gereesSupp)
                            <span class="ml-1 rounded-md bg-mineral px-1.5 py-0.5 text-xs font-medium text-slate-500">+{{ $gereesSupp }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if ($u->actif)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-koanda-light px-2.5 py-0.5 text-xs font-semibold text-koanda-dark ring-1 ring-inset ring-koanda/30">
                                <span class="h-1.5 w-1.5 rounded-full bg-koanda"></span>Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-mineral px-2.5 py-0.5 text-xs font-medium text-slate-500 ring-1 ring-inset ring-mist">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Désactivé
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('update', $u)
                                <form method="POST" action="{{ route('admin.utilisateurs.activation', $u) }}">
                                    @csrf @method('PATCH')
                                    <button class="rounded-md border border-mist px-2.5 py-1.5 text-xs font-medium text-slatetext transition hover:bg-mineral" title="{{ $u->actif ? 'Désactiver le compte' : 'Activer le compte' }}">
                                        {{ $u->actif ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.utilisateurs.edit', $u) }}" class="rounded-md bg-forest px-2.5 py-1.5 text-xs font-medium text-white transition hover:bg-forest-soft">Modifier</a>
                            @endcan
                            @can('delete', $u)
                                <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u) }}" onsubmit="return confirm('Supprimer définitivement ce compte ?');">
                                    @csrf @method('DELETE')
                                    <button class="rounded-md border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50" title="Supprimer">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-mineral text-xl text-slate-400">⚙</div>
                        <p class="mt-3 text-sm font-medium text-slate-500">Aucun utilisateur ne correspond à ces critères.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $utilisateurs->links() }}</div>
@endsection
