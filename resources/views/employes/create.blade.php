@extends('layouts.app')
@section('titre', 'Nouvel employé')
@section('rubrique', 'Gestion · Employés')

@section('contenu')
<div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('employes.store') }}">
        @csrf
        @include('employes._form')
        <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
            <a href="{{ route('employes.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Annuler</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
