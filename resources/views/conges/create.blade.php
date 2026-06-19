@extends('layouts.app')
@section('titre', 'Nouvelle demande de congé')
@section('rubrique', 'Congés · Nouvelle demande')

@section('contenu')
<form method="POST" action="{{ route('conges.store') }}">
    @csrf
    <x-form.card title="Demande de congé" subtitle="Précisez l'employé, le type et la période souhaitée">
        <x-form.section number="1" title="Bénéficiaire &amp; type" icon="user">
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id') == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
            <x-form.select label="Type de congé" name="type_conge" required :placeholder="false" col="sm:col-span-2">
                @foreach (['annuel' => 'Congé annuel', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'paternite' => 'Paternité', 'exceptionnel' => 'Exceptionnel', 'sans_solde' => 'Sans solde'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('type_conge') === $v)>{{ $l }}</option>
                @endforeach
            </x-form.select>
        </x-form.section>

        <x-form.section number="2" title="Période &amp; motif" icon="calendar">
            <x-form.input label="Date de début" name="date_debut" type="date" required :value="old('date_debut')" col="sm:col-span-2" />
            <x-form.input label="Date de fin" name="date_fin" type="date" required :value="old('date_fin')" col="sm:col-span-2" />
            <x-form.textarea label="Motif" name="motif" col="sm:col-span-2 lg:col-span-4" />
        </x-form.section>
    </x-form.card>
    <x-form.actions :cancel="route('conges.index')" label="Soumettre la demande" />
</form>
@endsection
