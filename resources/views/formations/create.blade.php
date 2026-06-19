@extends('layouts.app')
@section('titre', 'Nouvelle formation')
@section('rubrique', 'Formation · Nouvelle')

@section('contenu')
<form method="POST" action="{{ route('formations.store') }}">
    @csrf
    @include('formations._form')
    <x-form.actions :cancel="route('formations.index')" label="Créer la formation" />
</form>
@endsection
