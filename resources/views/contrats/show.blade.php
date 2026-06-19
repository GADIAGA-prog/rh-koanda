@extends('layouts.app')
@section('titre', 'Contrat — ' . $contrat->employe->nom_complet)
@section('rubrique', 'Gestion · Contrats')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Détail du contrat --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $contrat->type_contrat->libelle() }}</h2>
                <p class="text-sm text-slate-400">{{ $contrat->reference ?? 'Sans référence' }}</p>
            </div>
            <span class="inline-flex rounded-full bg-{{ $contrat->statut->couleur() }}-50 px-3 py-1 text-xs font-medium text-{{ $contrat->statut->couleur() }}-700">
                {{ $contrat->statut->libelle() }}
            </span>
        </div>

        @if ($contrat->aRenouveler())
            <div class="mt-4 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <span>⚠</span>
                Ce contrat arrive à échéance le {{ $contrat->date_fin->format('d/m/Y') }}
                (dans {{ $contrat->jours_avant_echeance }} jour(s)).
            </div>
        @endif

        <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Employé</dt><dd class="font-medium text-slate-700">{{ $contrat->employe->nom_complet }}</dd></div>
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Filiale</dt><dd class="font-medium text-slate-700">{{ $contrat->filiale->nom }}</dd></div>
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Date de début</dt><dd class="font-medium text-slate-700">{{ $contrat->date_debut->format('d/m/Y') }}</dd></div>
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Date de fin</dt><dd class="font-medium text-slate-700">{{ optional($contrat->date_fin)->format('d/m/Y') ?? '—' }}</dd></div>
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Salaire de base</dt><dd class="font-semibold text-slate-900">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} {{ $contrat->devise }}</dd></div>
            <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Devise</dt><dd class="font-medium text-slate-700">{{ $contrat->devise }}</dd></div>
        </dl>

        @if ($contrat->observations)
            <div class="mt-6">
                <p class="text-sm font-medium text-slate-700">Observations</p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-500">{{ $contrat->observations }}</p>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-4">
            <a href="{{ route('employes.show', $contrat->employe) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Fiche employé</a>
            @can('update', $contrat)
                <a href="{{ route('contrats.edit', $contrat) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Modifier</a>
            @endcan
            @can('delete', $contrat)
                <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Archiver ce contrat ?')">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Archiver</button>
                </form>
            @endcan
        </div>
    </div>

    {{-- Renouvellement --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-900">Renouvellement</h3>
        <p class="mt-1 text-xs text-slate-400">Clôture ce contrat (passé en « expiré ») et en crée un nouveau à la suite.</p>

        @can('renouveler', $contrat)
            @if ($contrat->statut->value === 'actif')
                <details class="mt-4 group">
                    <summary class="cursor-pointer list-none rounded-lg bg-koanda px-4 py-2 text-center text-sm font-medium text-white hover:bg-koanda-dark">
                        Renouveler ce contrat
                    </summary>
                    <form method="POST" action="{{ route('contrats.renouveler', $contrat) }}" class="mt-4 space-y-3 text-sm">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Type</label>
                            <select name="type_contrat" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
                                @foreach (\App\Models\Enums\TypeContrat::cases() as $type)
                                    <option value="{{ $type->value }}" @selected($contrat->type_contrat->value === $type->value)>{{ $type->libelle() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Nouvelle date de début *</label>
                            <input type="date" name="date_debut" required value="{{ optional($contrat->date_fin)->addDay()->format('Y-m-d') ?? old('date_debut') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Nouvelle date de fin</label>
                            <input type="date" name="date_fin" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Salaire de base</label>
                            <input type="number" step="0.01" min="0" name="salaire_base" value="{{ $contrat->salaire_base }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Référence</label>
                            <input name="reference" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-koanda focus:ring-koanda">
                        </div>
                        <button class="w-full rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white hover:bg-koanda-dark">Confirmer le renouvellement</button>
                    </form>
                </details>
            @else
                <p class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-xs text-slate-500">Seul un contrat actif peut être renouvelé.</p>
            @endif
        @endcan
    </div>
</div>
@endsection
