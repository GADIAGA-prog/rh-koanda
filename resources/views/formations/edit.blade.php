@extends('layouts.app')
@section('titre', 'Modifier la formation')
@section('rubrique', 'Formation · ' . $formation->intitule)

@section('contenu')
<form method="POST" action="{{ route('formations.update', $formation) }}">
    @csrf @method('PUT')
    @include('formations._form')
    <x-form.actions :cancel="route('formations.show', $formation)" label="Enregistrer" />
</form>
@endsection
