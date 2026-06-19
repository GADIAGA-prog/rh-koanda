@extends('layouts.app')
@section('titre', 'Rapports RH')
@section('rubrique', 'Rapports')

@section('contenu')
{{-- Cartes synthèse --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-mist bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Effectif total</p>
        <p class="mt-2 text-3xl font-bold text-forest">{{ number_format($totaux['effectif'], 0, ',', ' ') }}</p>
    </div>
    <div class="rounded-xl border border-mist bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Masse salariale (mois)</p>
        <p class="mt-2 text-3xl font-bold text-forest">{{ number_format($totaux['masse_salariale'], 0, ',', ' ') }} <span class="text-sm font-normal text-slate-400">XOF</span></p>
    </div>
    <div class="rounded-xl border border-mist bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Contrats à renouveler</p>
        <p class="mt-2 text-3xl font-bold text-forest">{{ $totaux['contrats_a_renouveler'] }}</p>
    </div>
</div>

{{-- Exports --}}
<div class="mt-6 flex flex-wrap items-center gap-2">
    <span class="text-sm font-medium text-slate-500">Exporter :</span>
    <a href="{{ route('rapports.export.employes') }}" class="rounded-lg border border-mist bg-white px-3 py-1.5 text-sm text-slatetext hover:bg-mineral">Employés (CSV)</a>
    <a href="{{ route('rapports.export.contrats') }}" class="rounded-lg border border-mist bg-white px-3 py-1.5 text-sm text-slatetext hover:bg-mineral">Contrats (CSV)</a>
    <a href="{{ route('rapports.export.conges') }}" class="rounded-lg border border-mist bg-white px-3 py-1.5 text-sm text-slatetext hover:bg-mineral">Congés (CSV)</a>
    <a href="{{ route('rapports.export.bulletins') }}" class="rounded-lg border border-mist bg-white px-3 py-1.5 text-sm text-slatetext hover:bg-mineral">Bulletins (CSV)</a>
    <a href="{{ route('rapports.consolide') }}" target="_blank" class="rounded-lg bg-forest px-3 py-1.5 text-sm font-medium text-white hover:bg-forest-soft">État consolidé (PDF imprimable)</a>
</div>

{{-- Tableau consolidé --}}
<div class="mt-6 overflow-hidden rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full divide-y divide-mist text-sm">
        <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Filiale</th>
                <th class="px-4 py-3 text-right">Effectif</th>
                <th class="px-4 py-3 text-right">Absentéisme</th>
                <th class="px-4 py-3 text-right">Retards</th>
                <th class="px-4 py-3 text-right">Turnover</th>
                <th class="px-4 py-3 text-right">Masse salariale</th>
                <th class="px-4 py-3 text-right">Contrats à renouveler</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($stats as $s)
                <tr class="hover:bg-mineral/40">
                    <td class="px-4 py-3 font-medium text-forest">{{ $s['filiale'] }} <span class="text-xs text-slate-400">{{ $s['code'] }}</span></td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ $s['effectif'] }}</td>
                    <td class="px-4 py-3 text-right {{ $s['taux_absenteisme'] > 10 ? 'text-rose-600 font-semibold' : 'text-slatetext' }}">{{ $s['taux_absenteisme'] }} %</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ $s['taux_retard'] }} %</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ $s['turnover'] }} %</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ number_format($s['masse_salariale'], 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-slatetext">{{ $s['contrats_a_renouveler'] }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Aucune donnée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
