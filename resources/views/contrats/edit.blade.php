@extends('layouts.app')
@section('titre', 'Modifier le contrat')
@section('rubrique', 'Contrats · ' . $contrat->employe->nom_complet)

@section('contenu')
<form method="POST" action="{{ route('contrats.update', $contrat) }}">
    @csrf
    @method('PUT')
    @include('contrats._form', ['employeFige' => true])
    <x-form.actions :cancel="route('contrats.show', $contrat)" label="Enregistrer les modifications" />
</form>
@endsection
