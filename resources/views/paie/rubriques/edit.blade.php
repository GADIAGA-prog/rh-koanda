@extends('layouts.app')
@section('titre', 'Modifier la rubrique')
@section('rubrique', 'Paie · Rubriques')

@section('contenu')
<form method="POST" action="{{ route('rubriques.update', $rubrique) }}">
    @csrf @method('PUT')
    <x-form.card title="Modifier la rubrique" subtitle="{{ $rubrique->libelle }}">
        @include('paie.rubriques._form')
    </x-form.card>
    <x-form.actions :cancel="route('rubriques.index')" label="Enregistrer" />
</form>
@endsection
