@extends('layouts.app')
@section('titre', 'Pointages')
@section('rubrique', 'Présence & absences · Pointages')

@section('contenu')
{{-- Filtres --}}
<form method="GET" class="flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-medium text-slate-500">Filiale</label>
        <select name="filiale_id" onchange="this.form.submit()" class="mt-1 rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected($filialeId == $f->id)>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500">Mois</label>
        <input type="month" name="mois" value="{{ $mois->format('Y-m') }}" onchange="this.form.submit()" class="mt-1 rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest focus:border-koanda focus:ring-koanda">
    </div>
</form>

{{-- Saisie d'un pointage --}}
@can('create', App\Models\Presence::class)
<form method="POST" action="{{ route('presences.store') }}" class="mt-5">
    @csrf
    <x-form.card title="Saisie d'un pointage" subtitle="Arrivée, départ et statut d'un employé pour une journée">
        <x-form.section number="1" title="Pointage" icon="calendar">
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id') == $emp->id)>{{ $emp->nom_complet }}</option>
                @endforeach
            </x-form.select>
            <x-form.input label="Date" name="date_presence" type="date" required :value="old('date_presence', now()->toDateString())" />
            <x-form.input label="Heure d'arrivée" name="heure_arrivee" type="time" :value="old('heure_arrivee')" />
            <x-form.input label="Heure de départ" name="heure_depart" type="time" :value="old('heure_depart')" />
            <x-form.select label="Statut" name="statut" required :placeholder="false">
                @foreach (\App\Models\Enums\StatutPresence::cases() as $s)
                    <option value="{{ $s->value }}" @selected(old('statut','present') === $s->value)>{{ $s->libelle() }}</option>
                @endforeach
            </x-form.select>
            <x-form.input label="Commentaire" name="commentaire" :value="old('commentaire')" col="sm:col-span-2 lg:col-span-3" />
        </x-form.section>
        <div class="px-6 pb-6">
            <button class="inline-flex items-center gap-2 rounded-lg bg-koanda px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-koanda-dark">
                <x-icon name="check" class="h-4 w-4" /> Enregistrer le pointage
            </button>
        </div>
    </x-form.card>
</form>
@endcan

{{-- Grille mensuelle --}}
<div class="mt-6 overflow-x-auto rounded-2xl border border-mist bg-white shadow-sm">
    <table class="min-w-full text-xs">
        <thead class="bg-mineral text-slate-400">
            <tr>
                <th class="sticky left-0 z-10 bg-mineral px-3 py-2 text-left font-semibold uppercase tracking-wider">Employé</th>
                @for ($j = 1; $j <= $fin->day; $j++)
                    @php $jour = $debut->copy()->day($j); @endphp
                    <th class="px-1.5 py-2 text-center font-medium {{ $jour->isWeekend() ? 'text-rose-300' : '' }}">{{ $j }}</th>
                @endfor
            </tr>
        </thead>
        <tbody class="divide-y divide-mist/60">
            @forelse ($employes as $emp)
                <tr class="hover:bg-mineral/40">
                    <td class="sticky left-0 z-10 whitespace-nowrap bg-white px-3 py-2 font-medium text-forest">{{ $emp->nom_complet }}</td>
                    @for ($j = 1; $j <= $fin->day; $j++)
                        @php $p = $presences[$emp->id][$j] ?? null; @endphp
                        <td class="px-1 py-1 text-center">
                            @if ($p)
                                <span title="{{ $p->statut->libelle() }}{{ $p->heure_arrivee ? ' · '.$p->heure_arrivee : '' }}"
                                      class="inline-flex h-6 w-6 items-center justify-center rounded bg-{{ $p->statut->couleur() }}-100 font-bold text-{{ $p->statut->couleur() }}-700">{{ $p->statut->abrege() }}</span>
                            @else
                                <span class="text-mist">·</span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @empty
                <tr><td colspan="{{ $fin->day + 1 }}" class="px-4 py-12 text-center text-slate-400">Aucun employé dans cette filiale.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<p class="mt-3 text-xs text-slate-400">Légende : <span class="font-semibold text-emerald-700">P</span> Présent · <span class="font-semibold text-amber-700">R</span> Retard · <span class="font-semibold text-rose-700">A</span> Absent · <span class="font-semibold text-sky-700">C</span> Congé</p>
@endsection
