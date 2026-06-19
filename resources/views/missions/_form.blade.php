@php $m = $mission ?? null; @endphp
<x-form.card title="Ordre de mission" subtitle="Objet, trajet et état de frais prévisionnel">
    <x-form.section number="1" title="Objet & bénéficiaire" icon="briefcase">
        <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
            @foreach ($employes as $emp)
                <option value="{{ $emp->id }}" @selected(old('employe_id', $m->employe_id ?? '') == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
            @endforeach
        </x-form.select>
        <x-form.input label="Objet de la mission" name="objet" required :value="$m->objet ?? null" col="sm:col-span-2" />
    </x-form.section>

    <x-form.section number="2" title="Trajet & durée" icon="sitemap">
        <x-form.input label="Lieu de départ" name="lieu_depart" :value="$m->lieu_depart ?? null" />
        <x-form.input label="Destination" name="destination" required :value="$m->destination ?? null" />
        <x-form.input label="Date de départ" name="date_depart" type="date" required :value="optional($m->date_depart ?? null)->format('Y-m-d')" />
        <x-form.input label="Date de retour" name="date_retour" type="date" required :value="optional($m->date_retour ?? null)->format('Y-m-d')" />
        <x-form.input label="Nombre de jours" name="nombre_jours" type="number" min="1" :value="$m->nombre_jours ?? null" hint="Laisser vide = calculé d'après les dates" />
        <x-form.input label="Moyen de transport" name="moyen_transport" :value="$m->moyen_transport ?? null" placeholder="Véhicule, avion…" />
    </x-form.section>

    <x-form.section number="3" title="État de frais" icon="document">
        <x-form.input label="Indemnité journalière" name="indemnite_journaliere" type="number" step="0.01" min="0" required :value="$m->indemnite_journaliere ?? 0" />
        <x-form.input label="Autres frais" name="autres_frais" type="number" step="0.01" min="0" :value="$m->autres_frais ?? 0" />
        <x-form.select label="Devise" name="devise" required :placeholder="false">
            @foreach (['XOF' => 'XOF (Franc CFA)', 'EUR' => 'EUR', 'USD' => 'USD'] as $code => $lib)
                <option value="{{ $code }}" @selected(old('devise', $m->devise ?? 'XOF') === $code)>{{ $lib }}</option>
            @endforeach
        </x-form.select>
        <x-form.textarea label="Observations" name="observations" :value="$m->observations ?? null" col="sm:col-span-2 lg:col-span-4" />
    </x-form.section>
</x-form.card>
