@php $enfants = $enfantsParManager[$employe->id] ?? collect(); @endphp
<div class="@if($niveau > 0) ml-6 border-l border-mist pl-6 @endif">
    <div class="relative my-2 inline-flex items-center gap-3 rounded-xl border border-mist bg-mineral/40 px-4 py-2.5 shadow-sm">
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-koanda text-xs font-semibold text-white">{{ $employe->initiales }}</div>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-forest">{{ $employe->nom_complet }}</p>
            <p class="text-xs text-slate-400">{{ $employe->poste->intitule ?? 'Poste non défini' }}</p>
        </div>
        @if ($enfants->isNotEmpty())
            <span class="ml-1 rounded-full bg-koanda-light px-2 py-0.5 text-xs font-medium text-koanda-dark">{{ $enfants->count() }}</span>
        @endif
    </div>
    @foreach ($enfants as $enfant)
        @include('organisation._noeud', ['employe' => $enfant, 'enfantsParManager' => $enfantsParManager, 'niveau' => $niveau + 1])
    @endforeach
</div>
