@extends('layouts.app')
@section('titre', 'Utilisateurs')
@section('rubrique', 'Administration')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $utilisateurs->total() }} compte(s)</p>
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.utilisateurs.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            + Nouvel utilisateur
        </a>
    @endcan
</div>

{{-- Filtres --}}
<form method="GET" class="mt-4 grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
    <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom ou email…"
           class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    <select name="role" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Tous les rôles</option>
        @foreach ($roles as $r)
            <option value="{{ $r }}" @selected(($filtres['role'] ?? '') === $r)>{{ $r }}</option>
        @endforeach
    </select>
    <select name="actif" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Tous les statuts</option>
        <option value="1" @selected(($filtres['actif'] ?? '') === '1')>Actifs</option>
        <option value="0" @selected(($filtres['actif'] ?? '') === '0')>Désactivés</option>
    </select>
    <div class="flex gap-2">
        <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filtrer</button>
        <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Réinit.</a>
    </div>
</form>

{{-- Tableau --}}
<div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Utilisateur</th>
                <th class="px-4 py-3">Rôle</th>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($utilisateurs as $u)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $u->name }}</p>
                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $u->roles->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $u->filiale->nom ?? 'Groupe' }}</td>
                    <td class="px-4 py-3">
                        @if ($u->actif)
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Actif</span>
                        @else
                            <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-medium text-rose-700">Désactivé</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @can('update', $u)
                                <form method="POST" action="{{ route('admin.utilisateurs.activation', $u) }}">
                                    @csrf @method('PATCH')
                                    <button class="rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                                        {{ $u->actif ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.utilisateurs.edit', $u) }}" class="rounded-md bg-slate-900 px-3 py-1 text-xs font-medium text-white hover:bg-slate-700">Modifier</a>
                            @endcan
                            @can('delete', $u)
                                <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u) }}" onsubmit="return confirm('Supprimer ce compte ?');">
                                    @csrf @method('DELETE')
                                    <button class="rounded-md border border-rose-300 px-3 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">Aucun utilisateur.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $utilisateurs->links() }}</div>
@endsection
