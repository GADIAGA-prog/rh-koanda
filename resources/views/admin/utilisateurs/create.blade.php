@extends('layouts.app')
@section('titre', 'Nouvel utilisateur')
@section('rubrique', 'Administration · Utilisateurs')

@section('contenu')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-koanda-dark">← Retour aux utilisateurs</a>

    <form method="POST" action="{{ route('admin.utilisateurs.store') }}" class="mt-3 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
        @csrf
        <x-form.banner title="Créer un compte" subtitle="Renseigne l'identité, le rôle et le périmètre du nouvel utilisateur" />

        <div class="p-6">
            @include('admin.utilisateurs._form')
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-mist bg-mineral px-6 py-4">
            <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-mist bg-white px-4 py-2 text-sm text-slatetext transition hover:bg-mineral">
                <x-icon name="x" class="h-4 w-4" /> Annuler
            </a>
            <button class="inline-flex items-center gap-2 rounded-lg bg-koanda px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">
                <x-icon name="check" class="h-4 w-4" /> Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection
