@extends('layouts.app')
@section('titre', 'Nouvelle mesure disciplinaire')
@section('rubrique', 'Discipline · Nouvelle')

@section('contenu')
<form method="POST" action="{{ route('sanctions.store') }}" enctype="multipart/form-data">
    @csrf
    <x-form.card title="Mesure disciplinaire" subtitle="Confidentialité renforcée — accès réservé">
        <x-form.section number="1" title="Détail de la mesure" icon="gavel">
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id', $employeChoisi) == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
            <x-form.select label="Type" name="type" required :placeholder="false">
                @foreach (\App\Models\Enums\TypeSanction::cases() as $t)
                    <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ $t->libelle() }}</option>
                @endforeach
            </x-form.select>
            <x-form.input label="Date" name="date_sanction" type="date" required :value="old('date_sanction', now()->toDateString())" />
            <x-form.textarea label="Motif" name="motif" required :value="old('motif')" col="sm:col-span-2 lg:col-span-4" rows="4" />
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slatetext">Pièce jointe</label>
                <input type="file" name="document" class="mt-1.5 w-full rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest file:mr-3 file:rounded file:border-0 file:bg-koanda-light file:px-3 file:py-1 file:text-koanda-dark">
                <p class="mt-1 text-xs text-slate-400">PDF, JPG, PNG, DOC — 10 Mo max.</p>
                @error('document')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </x-form.section>
    </x-form.card>
    <x-form.actions :cancel="route('sanctions.index')" label="Enregistrer la mesure" />
</form>
@endsection
