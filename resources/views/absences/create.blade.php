@extends('layouts.app')
@section('titre', 'Nouvelle absence')
@section('rubrique', 'Présence & absences · Absences')

@section('contenu')
<form method="POST" action="{{ route('absences.store') }}" enctype="multipart/form-data">
    @csrf
    @include('absences._form')
    <x-form.actions :cancel="route('absences.index')" label="Enregistrer l'absence" />
</form>
@endsection
