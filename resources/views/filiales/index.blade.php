@extends('layouts.app')
@section('titre', 'Filiales du groupe')
@section('rubrique', 'Gestion')

@section('contenu')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($filiales as $filiale)
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">{{ $filiale->code }}</span>
                <span class="text-xs {{ $filiale->statut ? 'text-emerald-600' : 'text-slate-400' }}">{{ $filiale->statut ? 'Active' : 'Inactive' }}</span>
            </div>
            <h3 class="mt-3 font-semibold text-slate-900">{{ $filiale->nom }}</h3>
            <p class="text-sm text-slate-400">{{ $filiale->domaine }}</p>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-sm">
                <span class="text-slate-400">{{ $filiale->ville }}</span>
                <span class="font-semibold text-slate-900">{{ $filiale->employes_count }} employés</span>
            </div>
        </div>
    @endforeach
</div>
@endsection
