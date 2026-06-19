@extends('layouts.app')
@section('titre', "Enregistrement d'un employé")
@section('rubrique', 'Employés · Nouvel employé')

@section('contenu')
<form method="POST" action="{{ route('employes.store') }}">
    @csrf
    @include('employes._form')
    <x-form.actions :cancel="route('employes.index')" label="Enregistrer l'employé" />
</form>
@endsection
