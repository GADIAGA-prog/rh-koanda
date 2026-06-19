@extends('layouts.app')
@section('titre', 'Modifier l\'évaluation')
@section('rubrique', 'Performance · ' . $evaluation->employe->nom_complet)

@section('contenu')
<form method="POST" action="{{ route('evaluations.update', $evaluation) }}">
    @csrf @method('PUT')
    @include('performance._form')
    <x-form.actions :cancel="route('evaluations.show', $evaluation)" label="Enregistrer" />
</form>
@endsection
