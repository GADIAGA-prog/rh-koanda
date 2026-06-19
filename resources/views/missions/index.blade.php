@extends('layouts.app')
@section('titre', 'Missions')
@section('rubrique', 'Missions')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $missions->total() }} ordre(s) de mission</p>
    @can('create', App\Models\Mission::class)
        <a href="{{ route('missions.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Nouvelle mission</a>
    @endcan
</div>

<form method="GET" class="mt-4 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="statut" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Tous les statuts</option>
        @foreach (\App\Models\Enums\StatutMission::cases() as $s)
            <option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>
        @endforeach
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Objet</th>
                <th class="px-4 py-3">Destination</th>
                <th class="px-4 py-3">Période</th>
                <th class="px-4 py-3 text-right">Montant</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($missions as $mission)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $mission->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ \Illuminate\Support\Str::limit($mission->objet, 30) }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $mission->destination }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $mission->date_depart->format('d/m/Y') }} → {{ $mission->date_retour->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-forest">{{ number_format($mission->montant_total, 0, ',', ' ') }} {{ $mission->devise }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $mission->statut->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $mission->statut->couleur() }}-700">{{ $mission->statut->libelle() }}</span></td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('missions.show', $mission) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Aucune mission.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $missions->links() }}</div>
@endsection
