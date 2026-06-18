@extends('layouts.app')
@section('titre', 'Modifier — ' . $employe->nom_complet)
@section('rubrique', 'Gestion · Employés')

@section('contenu')
<div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('employes.update', $employe) }}">
        @csrf @method('PUT')
        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700">Matricule *</label>
            <input name="matricule" value="{{ old('matricule', $employe->matricule) }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        @include('employes._form')
        <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
            <a href="{{ route('employes.show', $employe) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Annuler</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
