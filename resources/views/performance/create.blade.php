@extends('layouts.app')
@section('titre', 'Nouvelle évaluation')
@section('rubrique', 'Performance · Nouvelle')

@section('contenu')
<form method="POST" action="{{ route('evaluations.store') }}">
    @csrf
    @include('performance._form')
    <x-form.actions :cancel="route('evaluations.index')" label="Enregistrer l'évaluation" />
</form>
@endsection
