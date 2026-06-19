@php $e = $evaluation ?? null; @endphp
<x-form.card title="Évaluation de performance" subtitle="Objectifs, note globale et prime proposée">
    <x-form.section number="1" title="Évaluation" icon="star">
        @if ($e)
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slatetext">Employé</label>
                <p class="mt-1.5 rounded-lg border border-mist bg-mineral px-3 py-2.5 text-sm text-forest">{{ $e->employe->nom_complet }}</p>
                <input type="hidden" name="employe_id" value="{{ $e->employe_id }}">
            </div>
        @else
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id') == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
        @endif
        <x-form.input label="Période" name="periode" required :value="$e->periode ?? null" placeholder="Ex : 2026-S1" />
        <x-form.input label="Note globale (/20)" name="note_globale" type="number" step="0.01" min="0" max="20" :value="$e->note_globale ?? null" />

        <x-form.textarea label="Objectifs" name="objectifs" :value="$e->objectifs ?? null" col="sm:col-span-2 lg:col-span-2" rows="4" />
        <x-form.textarea label="Commentaire" name="commentaire" :value="$e->commentaire ?? null" col="sm:col-span-2 lg:col-span-2" rows="4" />

        <x-form.input label="Prime proposée" name="prime_proposee" type="number" step="0.01" min="0" :value="$e->prime_proposee ?? null" hint="Montant en XOF (optionnel)" />
    </x-form.section>
</x-form.card>
