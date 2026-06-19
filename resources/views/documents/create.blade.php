@extends('layouts.app')
@section('titre', 'Téléverser un document')
@section('rubrique', 'Documents RH · Nouveau')

@section('contenu')
<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
    @csrf
    <x-form.card title="Document RH" subtitle="Pièce justificative rattachée à un employé">
        <x-form.section number="1" title="Document" icon="document">
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id', $employeChoisi) == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
            <x-form.select label="Type de document" name="type_document" required :placeholder="false">
                @foreach (['contrat' => 'Contrat', 'diplome' => 'Diplôme', 'cnib' => 'CNIB', 'attestation' => 'Attestation', 'fiche_poste' => 'Fiche de poste', 'certificat' => 'Certificat', 'autre' => 'Autre'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('type_document') === $v)>{{ $l }}</option>
                @endforeach
            </x-form.select>
            <x-form.select label="Confidentialité" name="confidentialite" required :placeholder="false">
                @foreach (\App\Models\Enums\Confidentialite::cases() as $c)
                    <option value="{{ $c->value }}" @selected(old('confidentialite', 'rh') === $c->value)>{{ $c->libelle() }}</option>
                @endforeach
            </x-form.select>

            <x-form.input label="Titre" name="titre" required :value="old('titre')" col="sm:col-span-2" />
            <x-form.input label="Date d'expiration" name="date_expiration" type="date" :value="old('date_expiration')" hint="Optionnel (diplôme, certificat…)" />

            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-sm font-medium text-slatetext">Fichier <span class="text-rose-500">*</span></label>
                <input type="file" name="fichier" required class="mt-1.5 w-full rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest file:mr-3 file:rounded file:border-0 file:bg-koanda-light file:px-3 file:py-1 file:text-koanda-dark">
                <p class="mt-1 text-xs text-slate-400">PDF, JPG, PNG, DOC — 10 Mo max. Stockage privé.</p>
                @error('fichier')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </x-form.section>
    </x-form.card>
    <x-form.actions :cancel="route('documents.index')" label="Téléverser" />
</form>
@endsection
