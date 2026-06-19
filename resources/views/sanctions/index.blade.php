@extends('layouts.app')
@section('titre', 'Discipline & sanctions')
@section('rubrique', 'Discipline')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $sanctions->total() }} mesure(s) disciplinaire(s)</p>
    @can('create', App\Models\Sanction::class)
        <a href="{{ route('sanctions.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Nouvelle mesure</a>
    @endcan
</div>

<form method="GET" class="mt-4 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="type" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Tous les types</option>
        @foreach (\App\Models\Enums\TypeSanction::cases() as $t)
            <option value="{{ $t->value }}" @selected(($filtres['type'] ?? '') === $t->value)>{{ $t->libelle() }}</option>
        @endforeach
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Motif</th>
                <th class="px-4 py-3">Prononcé par</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($sanctions as $s)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $s->employe->nom_complet }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $s->type->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $s->type->couleur() }}-700">{{ $s->type->libelle() }}</span></td>
                    <td class="px-4 py-3 text-slatetext">{{ $s->date_sanction->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ \Illuminate\Support\Str::limit($s->motif, 50) }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $s->auteur->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            @if ($s->document)
                                <a href="{{ route('sanctions.download', $s) }}" class="text-sm font-medium text-slate-500 hover:text-forest">Pièce</a>
                            @endif
                            @can('delete', $s)
                                <form method="POST" action="{{ route('sanctions.destroy', $s) }}" onsubmit="return confirm('Supprimer cette sanction ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucune mesure disciplinaire.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sanctions->links() }}</div>
@endsection
