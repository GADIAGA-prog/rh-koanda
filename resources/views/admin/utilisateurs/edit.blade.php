@extends('layouts.app')
@section('titre', 'Modifier ' . $utilisateur->name)
@section('rubrique', 'Administration')

@section('contenu')
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Coordonnées, rôle, filiales --}}
    <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        @include('admin.utilisateurs._form')

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Annuler</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enregistrer</button>
        </div>
    </form>

    {{-- Réinitialisation du mot de passe --}}
    <form method="POST" action="{{ route('admin.utilisateurs.reset', $utilisateur) }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PATCH')
        <h2 class="text-sm font-semibold text-slate-900">Réinitialiser le mot de passe</h2>
        <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Confirmer</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Réinitialiser</button>
        </div>
    </form>
</div>
@endsection
