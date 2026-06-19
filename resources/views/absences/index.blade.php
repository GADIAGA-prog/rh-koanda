@extends('layouts.app')
@section('titre', 'Absences')
@section('rubrique', 'Présence & absences · Absences')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $absences->total() }} absence(s)</p>
    @can('create', App\Models\Absence::class)
        <a href="{{ route('absences.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Nouvelle absence</a>
    @endcan
</div>

<form method="GET" class="mt-4 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="justifiee" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Justifiée ou non</option>
        <option value="1" @selected(($filtres['justifiee'] ?? '') === '1')>Justifiées</option>
        <option value="0" @selected(($filtres['justifiee'] ?? '') === '0')>Non justifiées</option>
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3">Période</th>
                <th class="px-4 py-3">Motif</th>
                <th class="px-4 py-3">Justifiée</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($absences as $a)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $a->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $a->filiale->nom }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $a->date_debut->format('d/m/Y') }} → {{ $a->date_fin->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $a->motif ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($a->justifiee)
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Justifiée</span>
                        @else
                            <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-medium text-rose-700">Non justifiée</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            @if ($a->justificatif)
                                <a href="{{ route('absences.justificatif', $a) }}" class="text-sm font-medium text-slate-500 hover:text-forest">Pièce</a>
                            @endif
                            @can('update', $a)
                                <a href="{{ route('absences.edit', $a) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Modifier</a>
                            @endcan
                            @can('delete', $a)
                                <form method="POST" action="{{ route('absences.destroy', $a) }}" onsubmit="return confirm('Supprimer cette absence ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucune absence enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $absences->links() }}</div>
@endsection
