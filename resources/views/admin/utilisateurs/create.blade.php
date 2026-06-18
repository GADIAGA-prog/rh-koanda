@extends('layouts.app')
@section('titre', 'Nouvel utilisateur')
@section('rubrique', 'Administration')

@section('contenu')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-koanda-dark">← Retour aux utilisateurs</a>

    <form method="POST" action="{{ route('admin.utilisateurs.store') }}" class="mt-3 overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
        @csrf
        <div class="border-b border-mist px-6 py-4">
            <h2 class="font-display text-base font-bold text-forest">Créer un compte</h2>
            <p class="text-xs text-slate-400">Renseigne l'identité, le rôle et le périmètre du nouvel utilisateur.</p>
        </div>

        <div class="p-6">
            @include('admin.utilisateurs._form')
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-mist bg-mineral px-6 py-4">
            <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-mist bg-white px-4 py-2 text-sm text-slatetext transition hover:bg-mineral">Annuler</a>
            <button class="rounded-lg bg-koanda px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">Créer l'utilisateur</button>
        </div>
    </form>
</div>
@endsection
