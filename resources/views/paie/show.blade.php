@extends('layouts.app')
@section('titre', 'Bulletin de paie')
@section('rubrique', 'Paie · ' . $bulletin->employe->nom_complet)

@section('contenu')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-mist bg-white p-6 shadow-sm lg:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-forest">{{ $bulletin->employe->nom_complet }}</h2>
                <p class="text-sm text-slate-400">{{ $bulletin->filiale->nom }} · Période {{ $bulletin->periode }}</p>
            </div>
            <span class="inline-flex rounded-full bg-{{ $bulletin->statut->couleur() }}-50 px-3 py-1 text-xs font-medium text-{{ $bulletin->statut->couleur() }}-700">{{ $bulletin->statut->libelle() }}</span>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-mist">
            <table class="min-w-full divide-y divide-mist text-sm">
                <thead class="bg-mineral text-left text-xs uppercase tracking-wider text-slate-400">
                    <tr><th class="px-4 py-2">Libellé</th><th class="px-4 py-2 text-right">Base</th><th class="px-4 py-2 text-right">Taux</th><th class="px-4 py-2 text-right">Montant</th></tr>
                </thead>
                <tbody class="divide-y divide-mist/60">
                    @foreach ($bulletin->lignes as $l)
                        <tr>
                            <td class="px-4 py-2 text-forest">{{ $l->libelle }} <span class="ml-1 text-xs text-{{ $l->type->couleur() }}-600">({{ $l->type->libelle() }})</span></td>
                            <td class="px-4 py-2 text-right text-slate-500">{{ number_format($l->base, 0, ',', ' ') }}</td>
                            <td class="px-4 py-2 text-right text-slate-500">{{ $l->taux ? $l->taux.' %' : '—' }}</td>
                            <td class="px-4 py-2 text-right font-medium text-{{ $l->type->value === 'gain' ? 'emerald' : 'rose' }}-700">{{ $l->type->value === 'gain' ? '+' : '−' }} {{ number_format($l->montant, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-x-8 gap-y-2 text-sm sm:grid-cols-4">
            <div><dt class="text-slate-400">Brut</dt><dd class="font-semibold text-forest">{{ number_format($bulletin->salaire_brut, 0, ',', ' ') }}</dd></div>
            <div><dt class="text-slate-400">Cotisations</dt><dd class="font-semibold text-forest">{{ number_format($bulletin->total_cotisations, 0, ',', ' ') }}</dd></div>
            <div><dt class="text-slate-400">Retenues</dt><dd class="font-semibold text-forest">{{ number_format($bulletin->total_retenues, 0, ',', ' ') }}</dd></div>
            <div><dt class="text-slate-400">Coût employeur</dt><dd class="font-semibold text-forest">{{ number_format($bulletin->cout_employeur, 0, ',', ' ') }}</dd></div>
        </dl>
        <div class="mt-4 flex items-center justify-between rounded-xl bg-koanda-light px-5 py-3">
            <span class="font-semibold text-forest">Net à payer</span>
            <span class="text-xl font-bold text-koanda-dark">{{ number_format($bulletin->net_a_payer, 0, ',', ' ') }} XOF</span>
        </div>
    </div>

    <div class="space-y-4">
        <a href="{{ route('paie.imprimer', $bulletin) }}" target="_blank" class="block rounded-lg bg-forest px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-forest-soft">🖨 Bulletin imprimable</a>

        @can('update', $bulletin)
            <div class="rounded-2xl border border-mist bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-forest">Statut</h3>
                <div class="mt-3 space-y-2">
                    @foreach (\App\Models\Enums\StatutBulletin::cases() as $s)
                        @if ($s->value !== $bulletin->statut->value)
                            <form method="POST" action="{{ route('paie.statut', $bulletin) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="statut" value="{{ $s->value }}">
                                <button class="w-full rounded-lg border border-mist px-4 py-2 text-sm font-medium text-slatetext hover:bg-mineral">Marquer « {{ $s->libelle() }} »</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        @endcan
        <a href="{{ route('paie.index', ['periode' => $bulletin->periode]) }}" class="block text-center text-sm text-slate-500 hover:text-koanda-dark">← Retour aux bulletins</a>
    </div>
</div>
@endsection
