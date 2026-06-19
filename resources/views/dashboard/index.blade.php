@extends('layouts.app')
@section('titre', $estVueGroupe ? 'Tableau de bord Groupe' : 'Tableau de bord')
@section('rubrique', 'Pilotage RH')

@section('contenu')
@php
    $cartes = [
        ['Effectif actif', $indicateurs['effectif_total'], 'Employés en activité', 'indigo'],
        ['Contrats actifs', $indicateurs['contrats_actifs'], 'En cours de validité', 'emerald'],
        ['Contrats à renouveler', $indicateurs['contrats_expirant'], 'Échéance < 30 jours', 'amber'],
        ['Congés en attente', $indicateurs['conges_en_attente'], 'À valider', 'sky'],
    ];
@endphp

{{-- Cartes KPI --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($cartes as [$titre, $valeur, $sous, $couleur])
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <p class="text-sm font-medium text-slate-500">{{ $titre }}</p>
                <span class="h-2.5 w-2.5 rounded-full bg-{{ $couleur }}-500"></span>
            </div>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($valeur, 0, ',', ' ') }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $sous }}</p>
        </div>
    @endforeach
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Effectif par filiale --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="text-sm font-semibold text-slate-900">Effectif par filiale</h2>
        <p class="text-xs text-slate-400">Employés actifs</p>
        <div class="mt-4 h-72"><canvas id="chartFiliales"></canvas></div>
    </div>

    {{-- Répartition contrats --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900">Contrats</h2>
        <p class="text-xs text-slate-400">Répartition par statut</p>
        <div class="mt-4 h-72 flex items-center"><canvas id="chartContrats"></canvas></div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Répartition H/F --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900">Répartition Hommes / Femmes</h2>
        <div class="mt-6 space-y-4">
            @php $total = max(1, $sexe['hommes'] + $sexe['femmes']); @endphp
            <div>
                <div class="flex justify-between text-sm"><span class="text-slate-600">Hommes</span><span class="font-semibold text-slate-900">{{ $sexe['hommes'] }}</span></div>
                <div class="mt-1 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-indigo-600" style="width: {{ round($sexe['hommes'] / $total * 100) }}%"></div></div>
            </div>
            <div>
                <div class="flex justify-between text-sm"><span class="text-slate-600">Femmes</span><span class="font-semibold text-slate-900">{{ $sexe['femmes'] }}</span></div>
                <div class="mt-1 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-amber-500" style="width: {{ round($sexe['femmes'] / $total * 100) }}%"></div></div>
            </div>
        </div>
    </div>

    {{-- Tableau consolidé --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="text-sm font-semibold text-slate-900">Effectifs consolidés</h2>
        <div class="mt-3 overflow-hidden rounded-lg border border-slate-100">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-2">Filiale</th>
                        <th class="px-4 py-2">Code</th>
                        <th class="px-4 py-2 text-right">Effectif</th>
                        @if ($tauxPresence->isNotEmpty())
                            <th class="px-4 py-2 text-right" title="Absentéisme ce mois">Absent.</th>
                            <th class="px-4 py-2 text-right" title="Retards ce mois">Retards</th>
                        @endif
                        @if ($masseSalariale->isNotEmpty())
                            <th class="px-4 py-2 text-right" title="Masse salariale nette du mois">Masse sal.</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($effectifParFiliale as $ligne)
                        @php $t = $tauxPresence[$ligne['id']] ?? null; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2 font-medium text-slate-800">{{ $ligne['filiale'] }}</td>
                            <td class="px-4 py-2 text-slate-400">{{ $ligne['code'] }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-slate-900">{{ $ligne['effectif'] }}</td>
                            @if ($tauxPresence->isNotEmpty())
                                <td class="px-4 py-2 text-right {{ $t && $t['taux_absenteisme'] > 10 ? 'text-rose-600 font-semibold' : 'text-slate-500' }}">{{ $t ? $t['taux_absenteisme'].' %' : '—' }}</td>
                                <td class="px-4 py-2 text-right {{ $t && $t['taux_retard'] > 10 ? 'text-amber-600 font-semibold' : 'text-slate-500' }}">{{ $t ? $t['taux_retard'].' %' : '—' }}</td>
                            @endif
                            @if ($masseSalariale->isNotEmpty())
                                @php $ms = $masseSalariale[$ligne['id']] ?? null; @endphp
                                <td class="px-4 py-2 text-right text-slate-700">{{ $ms ? number_format($ms->masse, 0, ',', ' ') : '—' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Contrats à renouveler --}}
@can('contrat.view')
<div class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Contrats à renouveler</h2>
            <p class="text-xs text-slate-400">Échéance dans les 30 prochains jours</p>
        </div>
        <a href="{{ route('contrats.index', ['statut' => 'actif']) }}" class="text-xs font-medium text-koanda-dark hover:text-koanda">Tous les contrats →</a>
    </div>
    <div class="mt-3 overflow-hidden rounded-lg border border-slate-100">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-4 py-2">Employé</th>
                    <th class="px-4 py-2">Filiale</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Échéance</th>
                    <th class="px-4 py-2 text-right">Reste</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($aRenouveler as $contrat)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-medium text-slate-800">
                            <a href="{{ route('contrats.show', $contrat) }}" class="hover:text-koanda-dark">{{ $contrat->employe->nom_complet }}</a>
                        </td>
                        <td class="px-4 py-2 text-slate-500">{{ $contrat->filiale->code ?? $contrat->filiale->nom }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $contrat->type_contrat->libelle() }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $contrat->date_fin->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right">
                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">{{ $contrat->jours_avant_echeance }} j</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Aucun contrat à renouveler dans les 30 jours.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endcan

<script>
    const dataFiliales = @json($effectifParFiliale);
    const dataContrats = @json($contrats);

    new Chart(document.getElementById('chartFiliales'), {
        type: 'bar',
        data: {
            labels: dataFiliales.map(d => d.code),
            datasets: [{ label: 'Effectif', data: dataFiliales.map(d => d.effectif), backgroundColor: '#4F46E5', borderRadius: 6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('chartContrats'), {
        type: 'doughnut',
        data: {
            labels: ['Actifs', 'Expirés', 'À renouveler'],
            datasets: [{ data: [dataContrats.actifs, dataContrats.expires, dataContrats.expirant],
                backgroundColor: ['#10B981', '#94A3B8', '#F59E0B'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endsection
