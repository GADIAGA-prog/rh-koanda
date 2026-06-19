@extends('layouts.app')
@section('titre', 'Modifier le dossier')
@section('rubrique', 'Employés · ' . $employe->nom_complet)

@section('contenu')
<form method="POST" action="{{ route('employes.update', $employe) }}">
    @csrf
    @method('PUT')
    @include('employes._form')
    <x-form.actions :cancel="route('employes.show', $employe)" label="Enregistrer les modifications" />
</form>
@endsection
