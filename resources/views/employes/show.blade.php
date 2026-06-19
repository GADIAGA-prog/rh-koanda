@extends('layouts.app')
@section('titre', $employe->nom_complet)
@section('rubrique', 'Gestion · Employés')

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Carte identité --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col items-center text-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-600 text-2xl font-bold text-white">{{ $employe->initiales }}</div>
            <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $employe->nom_complet }}</h2>
            <p class="text-sm text-slate-400">{{ $employe->poste->intitule ?? 'Poste non défini' }}</p>
            <span class="mt-2 inline-flex rounded-full bg-{{ $employe->statut->couleur() }}-50 px-3 py-0.5 text-xs font-medium text-{{ $employe->statut->couleur() }}-700">{{ $employe->statut->libelle() }}</span>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Matricule</dt><dd class="font-medium text-slate-700">{{ $employe->matricule }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Filiale</dt><dd class="font-medium text-slate-700">{{ $employe->filiale->nom }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Département</dt><dd class="font-medium text-slate-700">{{ $employe->departement->nom ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Manager</dt><dd class="font-medium text-slate-700">{{ $employe->manager->nom_complet ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Téléphone</dt><dd class="font-medium text-slate-700">{{ $employe->telephone ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Embauche</dt><dd class="font-medium text-slate-700">{{ optional($employe->date_embauche)->format('d/m/Y') ?? '—' }}</dd></div>
        </dl>
        @can('update', $employe)
            <a href="{{ route('employes.edit', $employe) }}" class="mt-6 block rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Modifier le dossier</a>
        @endcan
    </div>

    {{-- Onglets contenu --}}
    <div class="space-y-6 lg:col-span-2">
        {{-- Contrats --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Contrats</h3>
                @can('create', App\Models\Contrat::class)
                    <a href="{{ route('contrats.create', ['employe_id' => $employe->id]) }}" class="text-xs font-medium text-koanda-dark hover:text-koanda">+ Ajouter</a>
                @endcan
            </div>

            @php $aRenouveler = $employe->contrats->filter->aRenouveler(); @endphp
            @if ($aRenouveler->isNotEmpty())
                <div class="mt-3 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">
                    <span>⚠</span> {{ $aRenouveler->count() }} contrat(s) à renouveler prochainement.
                </div>
            @endif

            <div class="mt-3 divide-y divide-slate-50">
                @forelse ($employe->contrats as $contrat)
                    <a href="{{ route('contrats.show', $contrat) }}" class="flex items-center justify-between py-3 text-sm hover:bg-slate-50">
                        <div>
                            <p class="font-medium text-slate-800">
                                {{ $contrat->type_contrat->libelle() }}
                                @if ($contrat->aRenouveler())<span class="ml-1 inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">à renouveler</span>@endif
                            </p>
                            <p class="text-xs text-slate-400">Du {{ $contrat->date_debut->format('d/m/Y') }}@if($contrat->date_fin) au {{ $contrat->date_fin->format('d/m/Y') }}@endif</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} {{ $contrat->devise }}</p>
                            <span class="text-xs text-{{ $contrat->statut->couleur() }}-600">{{ $contrat->statut->libelle() }}</span>
                        </div>
                    </a>
                @empty
                    <p class="py-3 text-sm text-slate-400">Aucun contrat enregistré.</p>
                @endforelse
            </div>
        </div>

        {{-- Congés récents --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-900">Congés récents</h3>
            <div class="mt-3 divide-y divide-slate-50">
                @forelse ($employe->conges->take(5) as $conge)
                    <div class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ $conge->type_conge->libelle() }}</p>
                            <p class="text-xs text-slate-400">{{ $conge->date_debut->format('d/m/Y') }} → {{ $conge->date_fin->format('d/m/Y') }} · {{ $conge->nombre_jours }} j</p>
                        </div>
                        <span class="inline-flex rounded-full bg-{{ $conge->statut_validation->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $conge->statut_validation->couleur() }}-700">{{ $conge->statut_validation->libelle() }}</span>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-400">Aucun congé enregistré.</p>
                @endforelse
            </div>
        </div>

        {{-- Discipline & sanctions --}}
        @can('viewAny', App\Models\Sanction::class)
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Discipline &amp; sanctions</h3>
                    @can('create', App\Models\Sanction::class)
                        <a href="{{ route('sanctions.create', ['employe_id' => $employe->id]) }}" class="text-xs font-medium text-koanda-dark hover:text-koanda">+ Ajouter</a>
                    @endcan
                </div>
                <div class="mt-3 divide-y divide-slate-50">
                    @forelse ($employe->sanctions as $s)
                        <div class="flex items-center justify-between py-3 text-sm">
                            <div>
                                <p class="font-medium text-slate-800">{{ $s->type->libelle() }}</p>
                                <p class="text-xs text-slate-400">{{ $s->date_sanction->format('d/m/Y') }} · {{ \Illuminate\Support\Str::limit($s->motif, 40) }}</p>
                            </div>
                            <span class="inline-flex rounded-full bg-{{ $s->type->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $s->type->couleur() }}-700">{{ $s->type->libelle() }}</span>
                        </div>
                    @empty
                        <p class="py-3 text-sm text-slate-400">Aucune sanction.</p>
                    @endforelse
                </div>
            </div>
        @endcan

        {{-- Documents --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Documents RH</h3>
                @can('create', App\Models\DocumentRh::class)
                    <a href="{{ route('documents.create', ['employe_id' => $employe->id]) }}" class="text-xs font-medium text-koanda-dark hover:text-koanda">+ Téléverser</a>
                @endcan
            </div>
            <div class="mt-3 divide-y divide-slate-50">
                @forelse ($employe->documents as $doc)
                    <div class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ $doc->titre }}</p>
                            <p class="text-xs text-slate-400">{{ ucfirst(str_replace('_',' ',$doc->type_document)) }} · {{ $doc->confidentialite->libelle() }}@if($doc->date_expiration) · expire le {{ $doc->date_expiration->format('d/m/Y') }}@endif</p>
                        </div>
                        @can('view', $doc)
                            <a href="{{ route('documents.download', $doc) }}" class="text-xs font-medium text-koanda-dark hover:text-koanda">Télécharger</a>
                        @endcan
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-400">Aucun document.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
