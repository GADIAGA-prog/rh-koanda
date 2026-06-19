@extends('layouts.app')
@section('titre', 'Modifier la mission')
@section('rubrique', 'Missions · ' . $mission->employe->nom_complet)

@section('contenu')
<form method="POST" action="{{ route('missions.update', $mission) }}">
    @csrf @method('PUT')
    @include('missions._form')
    <x-form.actions :cancel="route('missions.show', $mission)" label="Enregistrer" />
</form>
@endsection
