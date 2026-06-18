@php
    $estEdition = isset($utilisateur);
    $rolesActuels = old('role', $estEdition ? $utilisateur->roles->pluck('name')->first() : '');
    $filialesGerees = old('filiales_gerees', $estEdition ? $utilisateur->filialesGerees->pluck('id')->all() : []);
    $employeLie = old('employe_id', $estEdition ? $utilisateur->employe?->id : null);

    // En édition, l'employé déjà lié n'est pas dans « employesLibres » : on l'ajoute.
    $optionsEmployes = $employesLibres;
    if ($estEdition && $utilisateur->employe) {
        $optionsEmployes = $employesLibres->prepend($utilisateur->employe);
    }
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nom complet</label>
        <input type="text" name="name" value="{{ old('name', $utilisateur->name ?? '') }}" required
               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $utilisateur->email ?? '') }}" required
               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    @unless ($estEdition)
        <div>
            <label class="block text-sm font-medium text-slate-700">Mot de passe initial</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    @endunless

    <div>
        <label class="block text-sm font-medium text-slate-700">Rôle</label>
        <select name="role" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">— Choisir —</option>
            @foreach ($roles as $r)
                <option value="{{ $r }}" @selected($rolesActuels === $r)>{{ $r }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Filiale principale</label>
        <select name="filiale_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Aucune (vue Groupe)</option>
            @foreach ($filiales as $f)
                <option value="{{ $f->id }}" @selected((string) old('filiale_id', $utilisateur->filiale_id ?? '') === (string) $f->id)>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Filiales gérées (RH multi-filiales)</label>
        <p class="text-xs text-slate-400">Filiales supplémentaires visibles par ce compte, en plus de la filiale principale.</p>
        <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
            @foreach ($filiales as $f)
                <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="checkbox" name="filiales_gerees[]" value="{{ $f->id }}"
                           @checked(in_array($f->id, $filialesGerees))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $f->nom }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Lier à une fiche employé (optionnel)</label>
        <select name="employe_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Aucune</option>
            @foreach ($optionsEmployes as $e)
                <option value="{{ $e->id }}" @selected((string) $employeLie === (string) $e->id)>
                    {{ $e->nom_complet }} — {{ $e->matricule }}
                </option>
            @endforeach
        </select>
    </div>

    @unless ($estEdition)
        <div class="sm:col-span-2">
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="actif" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Compte actif dès la création
            </label>
        </div>
    @endunless
</div>
