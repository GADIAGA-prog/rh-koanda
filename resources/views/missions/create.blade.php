@extends('layouts.app')
@section('titre', 'Nouvel ordre de mission')
@section('rubrique', 'Missions · Nouveau')

@section('contenu')
<form method="POST" action="{{ route('missions.store') }}">
    @csrf
    @include('missions._form')
    <x-form.actions :cancel="route('missions.index')" label="Créer la mission" />
</form>
@endsection
