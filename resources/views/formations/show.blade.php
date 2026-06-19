@extends('layouts.app')
@section('titre', $formation->intitule)
@section('rubrique', 'Formation · Détail')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Infos --}}
    <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm">
        <span class="inline-flex rounded-full bg-{{ $formation->statut->couleur() }}-50 px-3 py-1 text-xs font-medium text-{{ $formation->statut->couleur() }}-700">{{ $formation->statut->libelle() }}</span>
        <h2 class="mt-3 text-lg font-semibold text-forest">{{ $formation->intitule }}</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Filiale</dt><dd class="font-medium text-slatetext">{{ $formation->filiale->nom }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Organisme</dt><dd class="font-medium text-slatetext">{{ $formation->organisme ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Période</dt><dd class="font-medium text-slatetext">{{ optional($formation->date_debut)->format('d/m/Y') ?? '—' }} → {{ optional($formation->date_fin)->format('d/m/Y') ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Coût</dt><dd class="font-semibold text-forest">{{ number_format($formation->cout, 0, ',', ' ') }} {{ $formation->devise }}</dd></div>
        </dl>
        @if ($formation->objectif)<p class="mt-4 border-t border-mist pt-3 text-sm text-slate-500">{{ $formation->objectif }}</p>@endif
        @can('update', $formation)
            <a href="{{ route('formations.edit', $formation) }}" class="mt-5 block rounded-lg border border-mist px-4 py-2 text-center text-sm font-medium text-slatetext hover:bg-mineral">Modifier</a>
        @endcan
    </div>

    {{-- Participants --}}
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-forest">Participants ({{ $formation->participants->count() }})</h3>

            @can('update', $formation)
                <form method="POST" action="{{ route('formations.participants.ajouter', $formation) }}" class="mt-3 flex flex-wrap items-end gap-2">
                    @csrf
                    <select name="employe_id" required class="flex-1 rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                        <option value="">Ajouter un employé…</option>
                        @foreach ($employes as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nom_complet }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-koanda px-4 py-2 text-sm font-semibold text-white hover:bg-koanda-dark">Ajouter</button>
                </form>
            @endcan

            <div class="mt-4 divide-y divide-mist/60">
                @forelse ($formation->participants as $p)
                    <div class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <div>
                            <p class="font-medium text-forest">{{ $p->nom_complet }}</p>
                            <p class="text-xs text-slate-400">{{ $p->pivot->present ? 'Présent' : 'Absent' }}@if($p->pivot->resultat) · {{ $p->pivot->resultat }}@endif</p>
                        </div>
                        @can('update', $formation)
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('formations.participants.maj', [$formation, $p]) }}" class="flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <label class="inline-flex items-center gap-1 text-xs text-slate-500"><input type="checkbox" name="present" value="1" @checked($p->pivot->present) class="rounded border-mist text-koanda"> Présent</label>
                                    <input name="resultat" value="{{ $p->pivot->resultat }}" placeholder="Résultat" class="w-28 rounded-lg border border-mist px-2 py-1 text-xs focus:border-koanda focus:ring-koanda">
                                    <button class="rounded-md bg-mineral px-2 py-1 text-xs font-medium text-slatetext hover:bg-mist">OK</button>
                                </form>
                                <form method="POST" action="{{ route('formations.participants.retirer', [$formation, $p]) }}" onsubmit="return confirm('Retirer ce participant ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-rose-600 hover:text-rose-700">Retirer</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-400">Aucun participant inscrit.</p>
                @endforelse
            </div>
        </div>

        {{-- Acquisition de compétences --}}
        @can('update', $formation)
            <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-forest">Acquisition de compétence</h3>
                <p class="text-xs text-slate-400">Enregistre une compétence acquise (niveau 1 à 5) pour un participant.</p>
                <form method="POST" action="{{ route('formations.competence', $formation) }}" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @csrf
                    <select name="employe_id" required class="rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                        <option value="">Participant…</option>
                        @foreach ($formation->participants as $p)
                            <option value="{{ $p->id }}">{{ $p->nom_complet }}</option>
                        @endforeach
                    </select>
                    <input name="libelle" required placeholder="Compétence" class="rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                    <input name="domaine" placeholder="Domaine (optionnel)" class="rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                    <div class="flex gap-2">
                        <select name="niveau" required class="rounded-lg border border-mist px-3 py-2 text-sm focus:border-koanda focus:ring-koanda">
                            @for ($i = 1; $i <= 5; $i++)<option value="{{ $i }}">Niveau {{ $i }}</option>@endfor
                        </select>
                        <button class="rounded-lg bg-koanda px-4 py-2 text-sm font-semibold text-white hover:bg-koanda-dark">OK</button>
                    </div>
                </form>
            </div>
        @endcan
    </div>
</div>
@endsection
