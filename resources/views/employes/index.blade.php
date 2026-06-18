@extends('layouts.app')
@section('titre', 'Employés')
@section('rubrique', 'Gestion')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $employes->total() }} employé(s)</p>
    @can('create', App\Models\Employe::class)
        <a href="{{ route('employes.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            + Nouvel employé
        </a>
    @endcan
</div>

{{-- Filtres --}}
<form method="GET" class="mt-4 grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
    <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom, matricule, email…"
           class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    <select name="filiale_id" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="statut" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Tous les statuts</option>
        @foreach (['actif' => 'Actif', 'suspendu' => 'Suspendu', 'conge' => 'En congé', 'depart' => 'Parti'] as $v => $l)
            <option value="{{ $v }}" @selected(($filtres['statut'] ?? '') === $v)>{{ $l }}</option>
        @endforeach
    </select>
    <div class="flex gap-2">
        <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filtrer</button>
        <a href="{{ route('employes.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Réinit.</a>
    </div>
</form>

{{-- Tableau --}}
<div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Matricule</th>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3">Poste</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($employes as $employe)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">{{ $employe->initiales }}</div>
                            <div>
                                <p class="font-medium text-slate-900">{{ $employe->nom_complet }}</p>
                                <p class="text-xs text-slate-400">{{ $employe->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $employe->matricule }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $employe->filiale->nom }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $employe->poste->intitule ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-{{ $employe->statut->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $employe->statut->couleur() }}-700">
                            {{ $employe->statut->libelle() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('employes.show', $employe) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Ouvrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucun employé ne correspond à ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $employes->links() }}</div>
@endsection
