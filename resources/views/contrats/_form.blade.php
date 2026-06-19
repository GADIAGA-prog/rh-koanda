@php
    $c = $contrat ?? null;
    $employeFige = $employeFige ?? false; // employé non modifiable (édition)
@endphp
<x-form.card title="Contrat de travail" subtitle="Renseignez les conditions contractuelles de l'employé">

    {{-- 1. Parties & nature --}}
    <x-form.section number="1" title="Parties &amp; nature du contrat" icon="document">
        @if ($employeFige && $c)
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slatetext">Employé</label>
                <p class="mt-1.5 rounded-lg border border-mist bg-mineral px-3 py-2.5 text-sm text-forest">{{ $c->employe->nom_complet }} — {{ $c->employe->matricule }}</p>
                <input type="hidden" name="employe_id" value="{{ $c->employe_id }}">
            </div>
        @else
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id', $c->employe_id ?? ($employeChoisi ?? '')) == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
        @endif

        <x-form.select label="Type de contrat" name="type_contrat" required :placeholder="false">
            @foreach (\App\Models\Enums\TypeContrat::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type_contrat', isset($c) ? $c->type_contrat->value : 'cdi') === $type->value)>{{ $type->libelle() }}</option>
            @endforeach
        </x-form.select>
        <x-form.select label="Statut" name="statut" required :placeholder="false">
            @foreach (\App\Models\Enums\StatutContrat::cases() as $statut)
                <option value="{{ $statut->value }}" @selected(old('statut', isset($c) ? $c->statut->value : 'actif') === $statut->value)>{{ $statut->libelle() }}</option>
            @endforeach
        </x-form.select>
    </x-form.section>

    {{-- 2. Période & rémunération --}}
    <x-form.section number="2" title="Période &amp; rémunération" icon="briefcase">
        <x-form.input label="Date de début" name="date_debut" type="date" required :value="optional($c->date_debut ?? null)->format('Y-m-d')" />
        <x-form.input label="Date de fin" name="date_fin" type="date" :value="optional($c->date_fin ?? null)->format('Y-m-d')" hint="CDD, stage…" />
        <x-form.input label="Salaire de base" name="salaire_base" type="number" step="0.01" min="0" required :value="$c->salaire_base ?? null" placeholder="Montant" />
        <x-form.select label="Devise" name="devise" required :placeholder="false">
            @foreach (['XOF' => 'XOF (Franc CFA)', 'EUR' => 'EUR (Euro)', 'USD' => 'USD (Dollar)'] as $code => $libelle)
                <option value="{{ $code }}" @selected(old('devise', $c->devise ?? 'XOF') === $code)>{{ $libelle }}</option>
            @endforeach
        </x-form.select>
        <x-form.input label="Référence" name="reference" :value="$c->reference ?? null" placeholder="N° du contrat" />
    </x-form.section>

    {{-- 3. Observations --}}
    <x-form.section number="3" title="Observations" icon="document">
        <x-form.textarea label="Observations" name="observations" :value="$c->observations ?? null" col="sm:col-span-2 lg:col-span-4" />
    </x-form.section>
</x-form.card>
