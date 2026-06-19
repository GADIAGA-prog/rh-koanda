@extends('layouts.app')
@section('titre', 'Modifier le poste')
@section('rubrique', 'Organisation · Postes')

@section('contenu')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('postes.update', $poste) }}">
        @csrf @method('PUT')
        <x-form.card title="Modifier le poste" subtitle="{{ $poste->intitule }}">
            <x-form.section number="1" title="Informations" icon="briefcase" cols="2">
                <x-form.select label="Filiale" name="filiale_id" required :placeholder="false">
                    @foreach ($filiales as $f)
                        <option value="{{ $f->id }}" @selected(old('filiale_id', $poste->filiale_id) == $f->id)>{{ $f->nom }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select label="Département" name="departement_id" placeholder="— Aucun —">
                    @foreach ($departements as $d)
                        <option value="{{ $d->id }}" @selected(old('departement_id', $poste->departement_id) == $d->id)>{{ $d->nom }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input label="Intitulé" name="intitule" required :value="$poste->intitule" />
                <x-form.input label="Catégorie" name="categorie" :value="$poste->categorie" />
            </x-form.section>
        </x-form.card>
        <x-form.actions :cancel="route('postes.index')" label="Enregistrer" />
    </form>
</div>
@endsection
