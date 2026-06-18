@extends('layouts.app')
@section('titre', 'Nouvel utilisateur')
@section('rubrique', 'Administration')

@section('contenu')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('admin.utilisateurs.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @include('admin.utilisateurs._form')

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Annuler</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Créer l'utilisateur</button>
        </div>
    </form>
</div>
@endsection
