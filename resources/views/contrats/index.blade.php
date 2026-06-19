@extends('layouts.app')
@section('titre', 'Contrats')
@section('rubrique', 'Gestion')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $contrats->total() }} contrat(s)</p>
    @can('create', App\Models\Contrat::class)
        <a href="{{ route('contrats.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">
            + Nouveau contrat
        </a>
    @endcan
</div>

{{-- Filtres --}}
<form method="GET" class="mt-4 grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
    <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Référence, employé…"
           class="rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
    <select name="filiale_id" class="rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="type_contrat" class="rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
        <option value="">Tous les types</option>
        @foreach (\App\Models\Enums\TypeContrat::cases() as $type)
            <option value="{{ $type->value }}" @selected(($filtres['type_contrat'] ?? '') === $type->value)>{{ $type->libelle() }}</option>
        @endforeach
    </select>
    <select name="statut" class="rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
        <option value="">Tous les statuts</option>
        @foreach (\App\Models\Enums\StatutContrat::cases() as $statut)
            <option value="{{ $statut->value }}" @selected(($filtres['statut'] ?? '') === $statut->value)>{{ $statut->libelle() }}</option>
        @endforeach
    </select>
    <div class="flex gap-2">
        <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filtrer</button>
        <a href="{{ route('contrats.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Réinit.</a>
    </div>
</form>

{{-- Tableau --}}
<div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Période</th>
                <th class="px-4 py-3 text-right">Salaire</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($contrats as $contrat)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $contrat->employe->nom_complet }}</p>
                        <p class="text-xs text-slate-400">{{ $contrat->reference ?? $contrat->employe->matricule }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $contrat->filiale->nom }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $contrat->type_contrat->libelle() }}</td>
                    <td class="px-4 py-3 text-slate-500">
                        {{ $contrat->date_debut->format('d/m/Y') }}@if($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }}@else <span class="text-slate-300">→ —</span>@endif
                        @if ($contrat->aRenouveler())
                            <span class="ml-1 inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">à renouveler</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-slate-700">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} {{ $contrat->devise }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-{{ $contrat->statut->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $contrat->statut->couleur() }}-700">
                            {{ $contrat->statut->libelle() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('contrats.show', $contrat) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Ouvrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Aucun contrat ne correspond à ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $contrats->links() }}</div>
@endsection
