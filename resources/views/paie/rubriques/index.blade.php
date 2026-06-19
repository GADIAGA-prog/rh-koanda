@extends('layouts.app')
@section('titre', 'Rubriques de paie')
@section('rubrique', 'Paie · Catalogue des rubriques')

@section('contenu')
<div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
    ⚠ Les taux et montants sont paramétrables ici. Les valeurs de démonstration (CNSS, IUTS…) sont « à vérifier » par un comptable.
</div>

@can('create', App\Models\RubriquePaie::class)
<form method="POST" action="{{ route('rubriques.store') }}" class="mt-5">
    @csrf
    <x-form.card title="Nouvelle rubrique" subtitle="Élément de paie paramétrable (gain, cotisation ou retenue)">
        @include('paie.rubriques._form')
        <div class="px-6 pb-6">
            <button class="inline-flex items-center gap-2 rounded-lg bg-koanda px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                <x-icon name="check" class="h-4 w-4" /> Ajouter la rubrique
            </button>
        </div>
    </x-form.card>
</form>
@endcan

<div class="mt-6 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Code</th>
                <th class="px-4 py-3">Libellé</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Calcul</th>
                <th class="px-4 py-3">Portée</th>
                <th class="px-4 py-3 text-center">Active</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($rubriques as $r)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $r->code }}</td>
                    <td class="px-4 py-3 font-medium text-forest">{{ $r->libelle }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $r->type->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $r->type->couleur() }}-700">{{ $r->type->libelle() }}</span></td>
                    <td class="px-4 py-3 text-slatetext">{{ $r->mode_calcul->value === 'fixe' ? number_format($r->montant, 0, ',', ' ').' XOF' : $r->taux.' %' }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $r->filiale->nom ?? 'Groupe' }}</td>
                    <td class="px-4 py-3 text-center">{!! $r->actif ? '<span class="text-emerald-600">●</span>' : '<span class="text-slate-300">○</span>' !!}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            @can('update', $r)
                                <a href="{{ route('rubriques.edit', $r) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Modifier</a>
                            @endcan
                            @can('delete', $r)
                                <form method="POST" action="{{ route('rubriques.destroy', $r) }}" onsubmit="return confirm('Supprimer cette rubrique ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Aucune rubrique.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $rubriques->links() }}</div>
@endsection
