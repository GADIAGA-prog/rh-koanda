@extends('layouts.app')
@section('titre', 'Fonctions')
@section('rubrique', 'Organisation · Fonctions')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    @can('create', App\Models\Fonction::class)
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('fonctions.store') }}">
                @csrf
                <x-form.card title="Nouvelle fonction" subtitle="Référentiel commun au groupe">
                    <div class="space-y-4 px-6 py-6">
                        <x-form.input label="Libellé" name="libelle" required placeholder="Ex : Chef de projet" col="" />
                        <x-form.textarea label="Description" name="description" col="" />
                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-koanda px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                            <x-icon name="check" class="h-4 w-4" /> Créer la fonction
                        </button>
                    </div>
                </x-form.card>
            </form>
        </div>
    @endcan

    <div class="lg:col-span-2">
        <div class="overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
            <table class="min-w-full divide-y divide-mist text-sm">
                <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Fonction</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist/60">
                    @forelse ($fonctions as $f)
                        <tr class="hover:bg-mineral/50">
                            <td class="px-4 py-3 font-medium text-forest">{{ $f->libelle }}</td>
                            <td class="px-4 py-3 text-slatetext">{{ \Illuminate\Support\Str::limit($f->description, 70) ?: '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('update', $f)
                                        <a href="{{ route('fonctions.edit', $f) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Modifier</a>
                                    @endcan
                                    @can('delete', $f)
                                        <form method="POST" action="{{ route('fonctions.destroy', $f) }}" onsubmit="return confirm('Supprimer cette fonction ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-12 text-center text-slate-400">Aucune fonction.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $fonctions->links() }}</div>
    </div>
</div>
@endsection
