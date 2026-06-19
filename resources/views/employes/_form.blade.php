@php $e = $employe ?? null; @endphp
<x-form.card title="Fiche individuelle du personnel" subtitle="Veuillez remplir toutes les informations nécessaires">

    {{-- 1. Informations personnelles --}}
    <x-form.section number="1" title="Informations personnelles" icon="user">
        <x-form.input label="Matricule" name="matricule" :value="$e->matricule ?? null" placeholder="Ex : KG-EMP-0001" />
        <x-form.input label="Nom" name="nom" :value="$e->nom ?? null" required placeholder="Nom de famille" />
        <x-form.input label="Prénom" name="prenom" :value="$e->prenom ?? null" required placeholder="Prénom(s)" />
        <x-form.select label="Sexe" name="sexe" placeholder="Sélectionner">
            <option value="M" @selected(old('sexe', $e->sexe ?? '') === 'M')>Masculin</option>
            <option value="F" @selected(old('sexe', $e->sexe ?? '') === 'F')>Féminin</option>
        </x-form.select>

        <x-form.input label="Date de naissance" name="date_naissance" type="date" :value="optional($e->date_naissance ?? null)->format('Y-m-d')" />
        <x-form.input label="Lieu de naissance" name="lieu_naissance" :value="$e->lieu_naissance ?? null" placeholder="Ville / Pays" />
        <x-form.input label="N° CNIB" name="cnib" :value="$e->cnib ?? null" placeholder="Pièce d'identité" />
    </x-form.section>

    {{-- 2. Affectation dans le groupe --}}
    <x-form.section number="2" title="Affectation dans le groupe" icon="building">
        <x-form.select label="Filiale" name="filiale_id" required placeholder="Sélectionner une filiale">
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected(old('filiale_id', $e->filiale_id ?? '') == $f->id)>{{ $f->nom }}</option>
            @endforeach
        </x-form.select>
        <x-form.select label="Département / Direction" name="departement_id" placeholder="Sélectionner">
            @foreach ($departements as $d)
                <option value="{{ $d->id }}" @selected(old('departement_id', $e->departement_id ?? '') == $d->id)>{{ $d->nom }}</option>
            @endforeach
        </x-form.select>
        <x-form.select label="Poste occupé" name="poste_id" placeholder="Sélectionner">
            @foreach ($postes as $p)
                <option value="{{ $p->id }}" @selected(old('poste_id', $e->poste_id ?? '') == $p->id)>{{ $p->intitule }}</option>
            @endforeach
        </x-form.select>
        <x-form.select label="Supérieur hiérarchique" name="manager_id" placeholder="Sélectionner">
            @foreach ($managers as $m)
                <option value="{{ $m->id }}" @selected(old('manager_id', $e->manager_id ?? '') == $m->id)>{{ $m->nom_complet }}</option>
            @endforeach
        </x-form.select>

        <x-form.input label="Date d'affectation" name="date_embauche" type="date" :value="optional($e->date_embauche ?? null)->format('Y-m-d')" />
        <x-form.select label="Statut" name="statut" required :placeholder="false">
            @foreach (['actif' => 'Actif', 'suspendu' => 'Suspendu', 'conge' => 'En congé', 'depart' => 'Parti'] as $v => $l)
                <option value="{{ $v }}" @selected(old('statut', isset($e) ? $e->statut->value : 'actif') === $v)>{{ $l }}</option>
            @endforeach
        </x-form.select>
    </x-form.section>

    {{-- 3. Contacts --}}
    <x-form.section number="3" title="Contacts" icon="phone">
        <x-form.input label="Téléphone" name="telephone" type="tel" :value="$e->telephone ?? null" placeholder="+226 XX XX XX XX" />
        <x-form.input label="Email professionnel" name="email" type="email" :value="$e->email ?? null" placeholder="exemple@koandagroupe.com" />
        <x-form.textarea label="Adresse complète" name="adresse" :value="$e->adresse ?? null" placeholder="Adresse de résidence" col="sm:col-span-2 lg:col-span-4" />
    </x-form.section>
</x-form.card>
