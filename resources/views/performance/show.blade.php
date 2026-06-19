@extends('layouts.app')
@section('titre', 'Évaluation')
@section('rubrique', 'Performance · ' . $evaluation->employe->nom_complet)

@section('contenu')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-forest">{{ $evaluation->employe->nom_complet }}</h2>
                <p class="text-sm text-slate-400">{{ $evaluation->filiale->nom }} · Période {{ $evaluation->periode }}</p>
            </div>
            @if ($evaluation->note_globale !== null)
                <div class="text-right">
                    <p class="text-3xl font-bold text-koanda-dark">{{ rtrim(rtrim(number_format($evaluation->note_globale,2),'0'),'.') }}<span class="text-base text-slate-400">/20</span></p>
                </div>
            @endif
        </div>

        <div class="mt-6 space-y-4 text-sm">
            <div>
                <p class="font-medium text-slatetext">Objectifs</p>
                <p class="mt-1 whitespace-pre-line text-slate-500">{{ $evaluation->objectifs ?: '—' }}</p>
            </div>
            <div>
                <p class="font-medium text-slatetext">Commentaire</p>
                <p class="mt-1 whitespace-pre-line text-slate-500">{{ $evaluation->commentaire ?: '—' }}</p>
            </div>
            <div class="flex justify-between border-t border-mist pt-3">
                <span class="text-slate-400">Prime proposée</span>
                <span class="font-semibold text-forest">{{ $evaluation->prime_proposee ? number_format($evaluation->prime_proposee, 0, ',', ' ').' XOF' : '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Évaluateur</span>
                <span class="font-medium text-slatetext">{{ $evaluation->evaluateur->name ?? '—' }}</span>
            </div>
        </div>

        <div class="mt-6 flex gap-3 border-t border-mist pt-4">
            @can('update', $evaluation)
                <a href="{{ route('evaluations.edit', $evaluation) }}" class="rounded-lg border border-mist px-4 py-2 text-sm font-medium text-slatetext hover:bg-mineral">Modifier</a>
            @endcan
            <a href="{{ route('evaluations.index') }}" class="rounded-lg px-4 py-2 text-sm text-slate-500 hover:text-koanda-dark">← Retour</a>
        </div>
    </div>
</div>
@endsection
