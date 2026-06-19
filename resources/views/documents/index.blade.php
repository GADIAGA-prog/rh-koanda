@extends('layouts.app')
@section('titre', 'Documents RH')
@section('rubrique', 'Documents RH')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $documents->total() }} document(s)</p>
    @can('create', App\Models\DocumentRh::class)
        <a href="{{ route('documents.create') }}" class="rounded-lg bg-koanda px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-koanda-dark">+ Téléverser un document</a>
    @endcan
</div>

@if ($expirant > 0)
    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        ⚠ {{ $expirant }} document(s) arrivent à expiration dans les 30 prochains jours.
    </div>
@endif

<form method="GET" class="mt-4 flex flex-wrap gap-2">
    <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Toutes les filiales</option>
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </select>
    <select name="type_document" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
        <option value="">Tous les types</option>
        @foreach (['contrat' => 'Contrat', 'diplome' => 'Diplôme', 'cnib' => 'CNIB', 'attestation' => 'Attestation', 'fiche_poste' => 'Fiche de poste', 'certificat' => 'Certificat', 'autre' => 'Autre'] as $v => $l)
            <option value="{{ $v }}" @selected(($filtres['type_document'] ?? '') === $v)>{{ $l }}</option>
        @endforeach
    </select>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Titre</th>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Confidentialité</th>
                <th class="px-4 py-3">Expiration</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($documents as $doc)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $doc->titre }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $doc->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ ucfirst(str_replace('_', ' ', $doc->type_document)) }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $doc->confidentialite->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $doc->confidentialite->couleur() }}-700">{{ $doc->confidentialite->libelle() }}</span></td>
                    <td class="px-4 py-3 text-slatetext">
                        @if ($doc->date_expiration)
                            <span class="{{ $doc->date_expiration->isPast() ? 'text-rose-600 font-medium' : '' }}">{{ $doc->date_expiration->format('d/m/Y') }}</span>
                        @else — @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('documents.download', $doc) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Télécharger</a>
                            @can('delete', $doc)
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Supprimer ce document ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-medium text-rose-600 hover:text-rose-700">Suppr.</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucun document accessible.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->links() }}</div>
@endsection
