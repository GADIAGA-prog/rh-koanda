@extends('layouts.app')
@section('titre', 'Modifier ' . $utilisateur->name)
@section('rubrique', 'Administration')

@section('contenu')
@php
    $mots = preg_split('/\s+/', trim($utilisateur->name));
    $initiales = strtoupper(mb_substr($mots[0] ?? '', 0, 1) . (count($mots) > 1 ? mb_substr(end($mots), 0, 1) : ''));
@endphp
<div class="mx-auto max-w-4xl space-y-6">
    <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-koanda-dark">← Retour aux utilisateurs</a>

    {{-- Carte identité --}}
    <div class="flex items-center gap-4 rounded-xl border border-mist bg-white p-5 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-koanda text-base font-semibold text-white">{{ $initiales ?: '?' }}</div>
        <div class="min-w-0 flex-1">
            <p class="truncate font-display text-base font-bold text-forest">{{ $utilisateur->name }}</p>
            <p class="truncate text-sm text-slate-400">{{ $utilisateur->email }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-role-badge :role="$utilisateur->roles->pluck('name')->first()" />
            @if ($utilisateur->actif)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-koanda-light px-2.5 py-0.5 text-xs font-semibold text-koanda-dark ring-1 ring-inset ring-koanda/30"><span class="h-1.5 w-1.5 rounded-full bg-koanda"></span>Actif</span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-mineral px-2.5 py-0.5 text-xs font-medium text-slate-500 ring-1 ring-inset ring-mist"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Désactivé</span>
            @endif
        </div>
    </div>

    {{-- Coordonnées, rôle, filiales --}}
    <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}" class="overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
        @csrf @method('PUT')
        <div class="border-b border-mist px-6 py-4">
            <h2 class="font-display text-base font-bold text-forest">Coordonnées &amp; accès</h2>
        </div>
        <div class="p-6">
            @include('admin.utilisateurs._form')
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-mist bg-mineral px-6 py-4">
            <a href="{{ route('admin.utilisateurs.index') }}" class="rounded-lg border border-mist bg-white px-4 py-2 text-sm text-slatetext transition hover:bg-mineral">Annuler</a>
            <button class="rounded-lg bg-koanda px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">Enregistrer les modifications</button>
        </div>
    </form>

    {{-- Réinitialisation du mot de passe --}}
    <form method="POST" action="{{ route('admin.utilisateurs.reset', $utilisateur) }}" class="overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
        @csrf @method('PATCH')
        <div class="border-b border-mist px-6 py-4">
            <h2 class="font-display text-base font-bold text-forest">Réinitialiser le mot de passe</h2>
            <p class="text-xs text-slate-500">Définit un nouveau mot de passe pour ce compte.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slatetext">Nouveau mot de passe</label>
                <input type="password" name="password" required class="mt-1.5 w-full rounded-lg border-mist text-sm text-forest shadow-sm focus:border-koanda focus:ring-koanda">
            </div>
            <div>
                <label class="block text-sm font-medium text-slatetext">Confirmer</label>
                <input type="password" name="password_confirmation" required class="mt-1.5 w-full rounded-lg border-mist text-sm text-forest shadow-sm focus:border-koanda focus:ring-koanda">
            </div>
        </div>
        <div class="flex justify-end border-t border-mist bg-mineral px-6 py-4">
            <button class="rounded-lg border border-mist bg-white px-4 py-2 text-sm font-semibold text-slatetext transition hover:bg-mineral">Réinitialiser</button>
        </div>
    </form>
</div>
@endsection
