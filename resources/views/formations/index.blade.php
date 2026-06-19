@extends('layouts.app')
@section('titre', 'Formations')
@section('rubrique', 'Formation')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $formations->total() }} formation(s)</p>
    @can('create', App\Models\Formation::class)
        <a href="{{ route('formations.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Nouvelle formation</a>
    @endcan
</div>

{{-- Coûts par filiale --}}
@if ($couts->isNotEmpty())
    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($filiales as $f)
            @php $c = $couts[$f->id] ?? null; @endphp
            <div class="rounded-xl border border-mist bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-400">{{ $f->code ?? $f->nom }}</p>
                <p class="mt-1 text-lg font-bold text-forest">{{ $c ? number_format($c->cout_total, 0, ',', ' ') : 0 }} <span class="text-xs font-normal text-slate-400">XOF</span></p>
                <p class="text-xs text-slate-400">{{ $c->nombre ?? 0 }} formation(s)</p>
            </div>
        @endforeach
    </div>
@endif

<form method="GET" class="mt-5 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="statut" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Tous les statuts</option>
        @foreach (\App\Models\Enums\StatutFormation::cases() as $s)
            <option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>
        @endforeach
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Intitulé</th>
                <th class="px-4 py-3">Organisme</th>
                <th class="px-4 py-3">Dates</th>
                <th class="px-4 py-3 text-center">Participants</th>
                <th class="px-4 py-3 text-right">Coût</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($formations as $formation)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $formation->intitule }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $formation->organisme ?? '—' }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ optional($formation->date_debut)->format('d/m/Y') ?? '—' }}@if($formation->date_fin) → {{ $formation->date_fin->format('d/m/Y') }}@endif</td>
                    <td class="px-4 py-3 text-center text-slatetext">{{ $formation->participants_count }}</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ number_format($formation->cout, 0, ',', ' ') }} {{ $formation->devise }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $formation->statut->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $formation->statut->couleur() }}-700">{{ $formation->statut->libelle() }}</span></td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('formations.show', $formation) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Aucune formation.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $formations->links() }}</div>
@endsection
