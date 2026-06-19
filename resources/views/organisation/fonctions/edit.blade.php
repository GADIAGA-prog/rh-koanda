@extends('layouts.app')
@section('titre', 'Modifier la fonction')
@section('rubrique', 'Organisation · Fonctions')

@section('contenu')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('fonctions.update', $fonction) }}">
        @csrf @method('PUT')
        <x-form.card title="Modifier la fonction" subtitle="{{ $fonction->libelle }}">
            <x-form.section number="1" title="Informations" icon="identification" cols="1">
                <x-form.input label="Libellé" name="libelle" required :value="$fonction->libelle" />
                <x-form.textarea label="Description" name="description" :value="$fonction->description" col="" />
            </x-form.section>
        </x-form.card>
        <x-form.actions :cancel="route('fonctions.index')" label="Enregistrer" />
    </form>
</div>
@endsection
