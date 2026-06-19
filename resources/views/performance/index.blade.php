@extends('layouts.app')
@section('titre', 'Évaluations de performance')
@section('rubrique', 'Performance')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $evaluations->total() }} évaluation(s)</p>
    @can('create', App\Models\EvaluationPerformance::class)
        <a href="{{ route('evaluations.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Nouvelle évaluation</a>
    @endcan
</div>

<form method="GET" class="mt-4 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Période</th>
                <th class="px-4 py-3">Évaluateur</th>
                <th class="px-4 py-3 text-center">Note</th>
                <th class="px-4 py-3 text-right">Prime</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($evaluations as $ev)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $ev->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $ev->periode }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $ev->evaluateur->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if ($ev->note_globale !== null)
                            <span class="inline-flex rounded-full bg-{{ $ev->note_globale >= 10 ? 'emerald' : 'rose' }}-50 px-2.5 py-0.5 text-xs font-semibold text-{{ $ev->note_globale >= 10 ? 'emerald' : 'rose' }}-700">{{ rtrim(rtrim(number_format($ev->note_globale,2),'0'),'.') }}/20</span>
                        @else — @endif
                    </td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ $ev->prime_proposee ? number_format($ev->prime_proposee, 0, ',', ' ').' XOF' : '—' }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('evaluations.show', $ev) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucune évaluation.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $evaluations->links() }}</div>
@endsection
