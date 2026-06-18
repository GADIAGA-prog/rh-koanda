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
    $champ = 'mt-1.5 w-full rounded-lg border-mist bg-white text-sm text-forest shadow-sm focus:border-koanda focus:ring-koanda';
    $label = 'block text-sm font-medium text-slatetext';
@endphp

{{-- Section : Identité --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
    <div class="sm:col-span-1">
        <h3 class="font-display text-sm font-bold text-forest">Identité</h3>
        <p class="mt-1 text-xs text-slate-400">Nom affiché et adresse de connexion.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:col-span-2 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Nom complet</label>
            <input type="text" name="name" value="{{ old('name', $utilisateur->name ?? '') }}" required class="{{ $champ }}">
        </div>
        <div>
            <label class="{{ $label }}">Email</label>
            <input type="email" name="email" value="{{ old('email', $utilisateur->email ?? '') }}" required class="{{ $champ }}">
        </div>
        @unless ($estEdition)
            <div>
                <label class="{{ $label }}">Mot de passe initial</label>
                <input type="password" name="password" required class="{{ $champ }}">
                <p class="mt-1 text-xs text-slate-400">8 caractères minimum.</p>
            </div>
            <div>
                <label class="{{ $label }}">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="{{ $champ }}">
            </div>
        @endunless
    </div>
</div>

{{-- Section : Rôle & périmètre --}}
<div class="mt-6 grid grid-cols-1 gap-6 border-t border-mist pt-6 sm:grid-cols-3">
    <div class="sm:col-span-1">
        <h3 class="font-display text-sm font-bold text-forest">Rôle &amp; périmètre</h3>
        <p class="mt-1 text-xs text-slate-400">Le rôle définit les permissions. La filiale principale détermine les données visibles.</p>
    </div>
    <div class="space-y-4 sm:col-span-2">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Rôle</label>
                <select name="role" required class="{{ $champ }}">
                    <option value="">— Choisir —</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" @selected($rolesActuels === $r)>{{ \App\Models\User::ROLES_META[$r][0] ?? $r }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Filiale principale</label>
                <select name="filiale_id" class="{{ $champ }}">
                    <option value="">Aucune (vue Groupe)</option>
                    @foreach ($filiales as $f)
                        <option value="{{ $f->id }}" @selected((string) old('filiale_id', $utilisateur->filiale_id ?? '') === (string) $f->id)>{{ $f->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="{{ $label }}">Filiales gérées <span class="font-normal text-slate-400">(RH multi-filiales)</span></label>
            <p class="text-xs text-slate-400">Filiales supplémentaires visibles, en plus de la filiale principale.</p>
            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach ($filiales as $f)
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-mist px-3 py-2 text-sm transition hover:bg-mineral has-[:checked]:border-koanda has-[:checked]:bg-koanda-light">
                        <input type="checkbox" name="filiales_gerees[]" value="{{ $f->id }}" @checked(in_array($f->id, $filialesGerees)) class="rounded border-mist text-koanda focus:ring-koanda">
                        <span class="truncate text-slatetext">{{ $f->nom }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Section : Liaison --}}
<div class="mt-6 grid grid-cols-1 gap-6 border-t border-mist pt-6 sm:grid-cols-3">
    <div class="sm:col-span-1">
        <h3 class="font-display text-sm font-bold text-forest">Liaison employé</h3>
        <p class="mt-1 text-xs text-slate-400">Relie optionnellement ce compte à une fiche employé existante.</p>
    </div>
    <div class="space-y-4 sm:col-span-2">
        <div>
            <label class="{{ $label }}">Fiche employé (optionnel)</label>
            <select name="employe_id" class="{{ $champ }}">
                <option value="">Aucune</option>
                @foreach ($optionsEmployes as $e)
                    <option value="{{ $e->id }}" @selected((string) $employeLie === (string) $e->id)>{{ $e->nom_complet }} — {{ $e->matricule }}</option>
                @endforeach
            </select>
        </div>
        @unless ($estEdition)
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-mist px-3 py-2.5 text-sm has-[:checked]:border-koanda has-[:checked]:bg-koanda-light">
                <input type="checkbox" name="actif" value="1" checked class="rounded border-mist text-koanda focus:ring-koanda">
                <span class="font-medium text-slatetext">Compte actif dès la création</span>
            </label>
        @endunless
    </div>
</div>
