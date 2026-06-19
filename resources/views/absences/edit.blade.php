@extends('layouts.app')
@section('titre', 'Modifier l\'absence')
@section('rubrique', 'Présence & absences · Absences')

@section('contenu')
<form method="POST" action="{{ route('absences.update', $absence) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('absences._form')
    <x-form.actions :cancel="route('absences.index')" label="Enregistrer les modifications" />
</form>
@endsection
