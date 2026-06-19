@extends('layouts.app')
@section('titre', 'Nouveau contrat')
@section('rubrique', 'Contrats · Nouveau')

@section('contenu')
<form method="POST" action="{{ route('contrats.store') }}">
    @csrf
    @include('contrats._form')
    <x-form.actions :cancel="route('contrats.index')" label="Enregistrer le contrat" />
</form>
@endsection
