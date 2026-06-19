@extends('layouts.app')
@section('titre', 'Bulletins de paie')
@section('rubrique', 'Paie · Bulletins')

@section('contenu')
{{-- Génération --}}
@can('create', App\Models\BulletinPaie::class)
<form method="POST" action="{{ route('paie.generer') }}">
    @csrf
    <x-form.card title="Générer la paie" subtitle="Crée ou met à jour les bulletins d'une filiale pour une période">
        <x-form.section number="1" title="Période & filiale" icon="banknote" cols="3">
            <x-form.select label="Filiale" name="filiale_id" required placeholder="Choisir…">
                @foreach ($filiales as $f)
                    <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
                @endforeach
            </x-form.select>
            <x-form.input label="Période" name="periode" type="month" required :value="$periode" />
            <div class="flex items-end">
                <button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-koanda px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                    <x-icon name="banknote" class="h-4 w-4" /> Générer les bulletins
                </button>
            </div>
        </x-form.section>
    </x-form.card>
</form>
@endcan

{{-- Filtres liste --}}
<form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-medium text-slate-500">Période</label>
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" class="mt-1 rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500">Filiale</label>
        <select name="filiale_id" onchange="this.form.submit()" class="mt-1 rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
            <option value="">Toutes</option>
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected(($filtres['filiale_id'] ?? '') == $f->id)>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="mt-4 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3 text-right">Brut</th>
                <th class="px-4 py-3 text-right">Cotis.</th>
                <th class="px-4 py-3 text-right">Retenues</th>
                <th class="px-4 py-3 text-right">Net à payer</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($bulletins as $b)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $b->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slatetext">{{ $b->filiale->nom }}</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ number_format($b->salaire_brut, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ number_format($b->total_cotisations, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ number_format($b->total_retenues, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-forest">{{ number_format($b->net_a_payer, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-{{ $b->statut->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $b->statut->couleur() }}-700">{{ $b->statut->libelle() }}</span></td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('paie.show', $b) }}" class="text-sm font-medium text-koanda-dark hover:text-koanda">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400">Aucun bulletin pour {{ $periode }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $bulletins->links() }}</div>
@endsection
