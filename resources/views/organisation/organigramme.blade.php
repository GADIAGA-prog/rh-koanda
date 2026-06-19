@extends('layouts.app')
@section('titre', 'Organigramme')
@section('rubrique', 'Organisation · Organigramme')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $total }} employé(s) dans le périmètre</p>
    <form method="GET">
        <select name="filiale_id" onchange="this.form.submit()" class="rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected($filialeId == $f->id)>{{ $f->nom }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="mt-5 overflow-x-auto rounded-2xl border border-mist bg-white p-6 shadow-sm">
    @forelse ($racines as $racine)
        @include('organisation._noeud', ['employe' => $racine, 'enfantsParManager' => $enfantsParManager, 'niveau' => 0])
    @empty
        <p class="py-12 text-center text-slate-400">Aucun employé à afficher pour cette filiale.</p>
    @endforelse
</div>
@endsection
