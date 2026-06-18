@php $e = $employe ?? null; @endphp
<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nom *</label>
        <input name="nom" value="{{ old('nom', $e->nom ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Prénom *</label>
        <input name="prenom" value="{{ old('prenom', $e->prenom ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Sexe</label>
        <select name="sexe" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            <option value="M" @selected(old('sexe', $e->sexe ?? '') === 'M')>Masculin</option>
            <option value="F" @selected(old('sexe', $e->sexe ?? '') === 'F')>Féminin</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Date de naissance</label>
        <input type="date" name="date_naissance" value="{{ old('date_naissance', optional($e->date_naissance ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Téléphone</label>
        <input name="telephone" value="{{ old('telephone', $e->telephone ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $e->email ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Filiale *</label>
        <select name="filiale_id" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Choisir…</option>
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected(old('filiale_id', $e->filiale_id ?? '') == $f->id)>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Département</label>
        <select name="departement_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach ($departements as $d)
                <option value="{{ $d->id }}" @selected(old('departement_id', $e->departement_id ?? '') == $d->id)>{{ $d->nom }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Poste</label>
        <select name="poste_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach ($postes as $p)
                <option value="{{ $p->id }}" @selected(old('poste_id', $e->poste_id ?? '') == $p->id)>{{ $p->intitule }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Manager</label>
        <select name="manager_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach ($managers as $m)
                <option value="{{ $m->id }}" @selected(old('manager_id', $e->manager_id ?? '') == $m->id)>{{ $m->nom_complet }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Date d'embauche</label>
        <input type="date" name="date_embauche" value="{{ old('date_embauche', optional($e->date_embauche ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Statut *</label>
        <select name="statut" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach (['actif' => 'Actif', 'suspendu' => 'Suspendu', 'conge' => 'En congé', 'depart' => 'Parti'] as $v => $l)
                <option value="{{ $v }}" @selected(old('statut', isset($e) ? $e->statut->value : 'actif') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
</div>
