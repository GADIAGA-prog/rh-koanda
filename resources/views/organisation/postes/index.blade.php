@extends('layouts.app')
@section('titre', 'Postes')
@section('rubrique', 'Organisation · Postes')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    @can('create', App\Models\Poste::class)
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('postes.store') }}">
                @csrf
                <x-form.card title="Nouveau poste" subtitle="Rattaché à une filiale">
                    <div class="space-y-4 px-6 py-6">
                        <x-form.select label="Filiale" name="filiale_id" required placeholder="Choisir…" col="">
                            @foreach ($filiales as $f)
                                <option value="{{ $f->id }}" @selected(old('filiale_id') == $f->id)>{{ $f->nom }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.select label="Département" name="departement_id" placeholder="— Aucun —" col="">
                            @foreach ($departements as $d)
                                <option value="{{ $d->id }}" @selected(old('departement_id') == $d->id)>{{ $d->nom }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.input label="Intitulé du poste" name="intitule" required placeholder="Ex : Comptable" col="" />
                        <x-form.input label="Catégorie" name="categorie" placeholder="Ex : Cadre" col="" />
                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-koanda px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                            <x-icon name="check" class="h-4 w-4" /> Créer le poste
                        </button>
                    </div>
                </x-form.card>
            </form>
        </div>
    @endcan

    <div class="lg:col-span-2">
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
                <option value="">Toutes les filiales</option>
                @foreach ($filiales as $f)
                    <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
                @endforeach
            </select>
        </form>

        <div class="overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
            <table class="min-w-full divide-y divide-mist text-sm">
                <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Poste</th>
                        <th class="px-4 py-3">Département</th>
                        <th class="px-4 py-3">Filiale</th>
                        <th class="px-4 py-3 text-center">Effectif</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist/60">
                    @forelse ($postes as $p)
                        <tr class="hover:bg-mineral/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-forest">{{ $p->intitule }}</p>
                                <p class="text-xs text-slate-400">{{ $p->categorie ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slatetext">{{ $p->departement->nom ?? '—' }}</td>
                            <td class="px-4 py-3 text-slatetext">{{ $p->filiale->nom }}</td>
                            <td class="px-4 py-3 text-center text-slatetext">{{ $p->employes_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('update', $p)
                                        <a href="{{ route('postes.edit', $p) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Modifier</a>
                                    @endcan
                                    @can('delete', $p)
                                        <form method="POST" action="{{ route('postes.destroy', $p) }}" onsubmit="return confirm('Supprimer ce poste ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">Aucun poste.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $postes->links() }}</div>
    </div>
</div>
@endsection
