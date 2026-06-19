@extends('layouts.app')
@section('titre', 'Ordre de mission')
@section('rubrique', 'Missions · ' . $mission->employe->nom_complet)

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Détail --}}
    <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm lg:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-forest">{{ $mission->objet }}</h2>
                <p class="text-sm text-slate-400">{{ $mission->employe->nom_complet }} · {{ $mission->filiale->nom }}</p>
            </div>
            <span class="inline-flex rounded-full bg-{{ $mission->statut->couleur() }}-50 px-3 py-1 text-xs font-medium text-{{ $mission->statut->couleur() }}-700">{{ $mission->statut->libelle() }}</span>
        </div>

        @if ($mission->statut->value === 'refusee' && $mission->motif_refus)
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><strong>Motif du refus :</strong> {{ $mission->motif_refus }}</div>
        @endif

        <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
            <div class="flex justify-between border-b border-mist pb-2"><dt class="text-slate-400">Trajet</dt><dd class="font-medium text-forest">{{ $mission->lieu_depart ?? '—' }} → {{ $mission->destination }}</dd></div>
            <div class="flex justify-between border-b border-mist pb-2"><dt class="text-slate-400">Transport</dt><dd class="font-medium text-forest">{{ $mission->moyen_transport ?? '—' }}</dd></div>
            <div class="flex justify-between border-b border-mist pb-2"><dt class="text-slate-400">Départ</dt><dd class="font-medium text-forest">{{ $mission->date_depart->format('d/m/Y') }}</dd></div>
            <div class="flex justify-between border-b border-mist pb-2"><dt class="text-slate-400">Retour</dt><dd class="font-medium text-forest">{{ $mission->date_retour->format('d/m/Y') }}</dd></div>
        </dl>

        {{-- État de frais --}}
        <div class="mt-6 overflow-hidden rounded-xl border border-mist">
            <table class="min-w-full divide-y divide-mist text-sm">
                <tbody class="divide-y divide-mist">
                    <tr><td class="px-4 py-2 text-slate-500">Indemnité journalière</td><td class="px-4 py-2 text-right text-forest">{{ number_format($mission->indemnite_journaliere, 0, ',', ' ') }} {{ $mission->devise }}</td></tr>
                    <tr><td class="px-4 py-2 text-slate-500">Nombre de jours</td><td class="px-4 py-2 text-right text-forest">× {{ $mission->nombre_jours }}</td></tr>
                    <tr><td class="px-4 py-2 text-slate-500">Autres frais</td><td class="px-4 py-2 text-right text-forest">+ {{ number_format($mission->autres_frais, 0, ',', ' ') }} {{ $mission->devise }}</td></tr>
                    <tr class="bg-koanda-light"><td class="px-4 py-2.5 font-semibold text-forest">Montant total</td><td class="px-4 py-2.5 text-right text-base font-bold text-koanda-dark">{{ number_format($mission->montant_total, 0, ',', ' ') }} {{ $mission->devise }}</td></tr>
                </tbody>
            </table>
        </div>

        @if ($mission->observations)
            <p class="mt-4 whitespace-pre-line text-sm text-slate-500">{{ $mission->observations }}</p>
        @endif
    </div>

    {{-- Workflow --}}
    <div class="space-y-4">
        <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-forest">Workflow</h3>
            <div class="mt-4 space-y-2">
                @can('update', $mission)
                    <a href="{{ route('missions.edit', $mission) }}" class="block rounded-lg border border-mist px-4 py-2 text-center text-sm font-medium text-slatetext hover:bg-mineral">Modifier</a>
                    @if ($mission->statut->value === 'brouillon' || $mission->statut->value === 'refusee')
                        <form method="POST" action="{{ route('missions.soumettre', $mission) }}">
                            @csrf
                            <button class="w-full rounded-lg bg-koanda px-4 py-2 text-sm font-semibold text-white hover:bg-koanda-dark">Soumettre pour validation</button>
                        </form>
                    @endif
                @endcan

                @can('valider', $mission)
                    @if ($mission->statut->value === 'soumise')
                        <form method="POST" action="{{ route('missions.valider', $mission) }}">
                            @csrf
                            <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Valider</button>
                        </form>
                        <form method="POST" action="{{ route('missions.refuser', $mission) }}" class="space-y-2">
                            @csrf
                            <input name="motif_refus" placeholder="Motif du refus (optionnel)" class="w-full rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                            <button class="w-full rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Refuser</button>
                        </form>
                    @endif
                    @if ($mission->statut->value === 'validee')
                        <form method="POST" action="{{ route('missions.cloturer', $mission) }}">
                            @csrf
                            <button class="w-full rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Clôturer la mission</button>
                        </form>
                    @endif
                @endcan
            </div>
            @if ($mission->validateur)
                <p class="mt-4 border-t border-mist pt-3 text-xs text-slate-400">Traité par {{ $mission->validateur->name }} le {{ optional($mission->valide_le)->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        <a href="{{ route('missions.index') }}" class="block text-center text-sm text-slate-500 hover:text-koanda-dark">← Retour à la liste</a>
    </div>
</div>
@endsection
