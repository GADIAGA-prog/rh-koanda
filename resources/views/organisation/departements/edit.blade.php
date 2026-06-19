@extends('layouts.app')
@section('titre', 'Modifier le département')
@section('rubrique', 'Organisation · Départements')

@section('contenu')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('departements.update', $departement) }}">
        @csrf @method('PUT')
        <x-form.card title="Modifier le département" subtitle="{{ $departement->nom }}">
            <x-form.section number="1" title="Informations" icon="building" cols="2">
                <x-form.select label="Filiale" name="filiale_id" required :placeholder="false">
                    @foreach ($filiales as $f)
                        <option value="{{ $f->id }}" @selected(old('filiale_id', $departement->filiale_id) == $f->id)>{{ $f->nom }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select label="Site" name="site_id" placeholder="— Aucun —">
                    @foreach ($sites as $s)
                        <option value="{{ $s->id }}" @selected(old('site_id', $departement->site_id) == $s->id)>{{ $s->nom }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input label="Nom" name="nom" required :value="$departement->nom" />
                <x-form.input label="Code" name="code" :value="$departement->code" />
            </x-form.section>
        </x-form.card>
        <x-form.actions :cancel="route('departements.index')" label="Enregistrer" />
    </form>
</div>
@endsection
