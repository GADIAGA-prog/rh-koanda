@extends('layouts.app')
@section('titre', 'Nouvelle demande de congé')
@section('rubrique', 'Gestion · Congés')

@section('contenu')
<div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('conges.store') }}">
        @csrf
        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700">Employé *</label>
                <select name="employe_id" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Choisir…</option>
                    @foreach ($employes as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employe_id') == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Type de congé *</label>
                <select name="type_conge" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (['annuel' => 'Congé annuel', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'paternite' => 'Paternité', 'exceptionnel' => 'Exceptionnel', 'sans_solde' => 'Sans solde'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('type_conge') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Date de début *</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut') }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Date de fin *</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin') }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Motif</label>
                <textarea name="motif" rows="3" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('motif') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
            <a href="{{ route('conges.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Annuler</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Soumettre</button>
        </div>
    </form>
</div>
@endsection
