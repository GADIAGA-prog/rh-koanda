@extends('layouts.app')
@section('titre', 'Départements')
@section('rubrique', 'Organisation · Départements')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Création --}}
    @can('create', App\Models\Departement::class)
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('departements.store') }}">
                @csrf
                <x-form.card title="Nouveau département" subtitle="Rattaché à une filiale">
                    <div class="space-y-4 px-6 py-6">
                        <x-form.select label="Filiale" name="filiale_id" required placeholder="Choisir…" col="">
                            @foreach ($filiales as $f)
                                <option value="{{ $f->id }}" @selected(old('filiale_id') == $f->id)>{{ $f->nom }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.select label="Site" name="site_id" placeholder="— Aucun —" col="">
                            @foreach ($sites as $s)
                                <option value="{{ $s->id }}" @selected(old('site_id') == $s->id)>{{ $s->nom }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.input label="Nom du département" name="nom" required placeholder="Ex : Ressources Humaines" col="" />
                        <x-form.input label="Code" name="code" placeholder="Ex : RH" col="" />
                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-koanda px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                            <x-icon name="check" class="h-4 w-4" /> Créer le département
                        </button>
                    </div>
                </x-form.card>
            </form>
        </div>
    @endcan

    {{-- Liste --}}
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
                        <th class="px-4 py-3">Département</th>
                        <th class="px-4 py-3">Filiale</th>
                        <th class="px-4 py-3">Site</th>
                        <th class="px-4 py-3 text-center">Postes</th>
                        <th class="px-4 py-3 text-center">Effectif</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist/60">
                    @forelse ($departements as $d)
                        <tr class="hover:bg-mineral/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-forest">{{ $d->nom }}</p>
                                <p class="text-xs text-slate-400">{{ $d->code ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slatetext">{{ $d->filiale->nom }}</td>
                            <td class="px-4 py-3 text-slatetext">{{ $d->site->nom ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-slatetext">{{ $d->postes_count }}</td>
                            <td class="px-4 py-3 text-center text-slatetext">{{ $d->employes_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('update', $d)
                                        <a href="{{ route('departements.edit', $d) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Modifier</a>
                                    @endcan
                                    @can('delete', $d)
                                        <form method="POST" action="{{ route('departements.destroy', $d) }}" onsubmit="return confirm('Supprimer ce département ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucun département.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $departements->links() }}</div>
    </div>
</div>
@endsection
