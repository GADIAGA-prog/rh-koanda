@extends('layouts.app')
@section('titre', 'Congés')
@section('rubrique', 'Gestion')

@section('contenu')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">{{ $conges->total() }} demande(s)</p>
    @can('create', App\Models\Conge::class)
        <a href="{{ route('conges.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            + Nouvelle demande
        </a>
    @endcan
</div>

{{-- Filtres par statut --}}
<form method="GET" class="mt-4 flex flex-wrap gap-2">
    @php $statuts = ['' => 'Tous', 'en_attente' => 'En attente', 'valide' => 'Validés', 'refuse' => 'Refusés']; @endphp
    @foreach ($statuts as $v => $l)
        <a href="{{ route('conges.index', array_filter(['statut' => $v])) }}"
           class="rounded-lg border px-3 py-1.5 text-sm {{ ($filtres['statut'] ?? '') === $v ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            {{ $l }}
        </a>
    @endforeach
</form>

<div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-400">
            <tr>
                <th class="px-4 py-3">Employé</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Période</th>
                <th class="px-4 py-3">Jours</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($conges as $conge)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $conge->employe->nom_complet }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $conge->type_conge->libelle() }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $conge->date_debut->format('d/m/Y') }} → {{ $conge->date_fin->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $conge->nombre_jours }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-{{ $conge->statut_validation->couleur() }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $conge->statut_validation->couleur() }}-700">
                            {{ $conge->statut_validation->libelle() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('valider', $conge)
                            @if ($conge->statut_validation->value === 'en_attente')
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('conges.valider', $conge) }}">
                                        @csrf
                                        <button class="rounded-md bg-emerald-600 px-3 py-1 text-xs font-medium text-white hover:bg-emerald-700">Valider</button>
                                    </form>
                                    <form method="POST" action="{{ route('conges.refuser', $conge) }}">
                                        @csrf
                                        <button class="rounded-md border border-rose-300 px-3 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">Refuser</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">{{ $conge->validateur->name ?? '—' }}</span>
                            @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Aucune demande de congé.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $conges->links() }}</div>
@endsection
