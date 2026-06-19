@php $r = $rubrique ?? null; $estGroupe = auth()->user()->peutVoirToutLeGroupe(); @endphp
<x-form.section number="1" title="Paramètres de la rubrique" icon="banknote" cols="3">
    <x-form.input label="Code" name="code" required :value="$r->code ?? null" placeholder="Ex : TRANSPORT" />
    <x-form.input label="Libellé" name="libelle" required :value="$r->libelle ?? null" col="sm:col-span-2" />

    <x-form.select label="Type" name="type" required :placeholder="false">
        @foreach (\App\Models\Enums\TypeRubrique::cases() as $t)
            <option value="{{ $t->value }}" @selected(old('type', isset($r) ? $r->type->value : 'gain') === $t->value)>{{ $t->libelle() }}</option>
        @endforeach
    </x-form.select>
    <x-form.select label="Mode de calcul" name="mode_calcul" required :placeholder="false">
        @foreach (\App\Models\Enums\ModeCalcul::cases() as $mc)
            <option value="{{ $mc->value }}" @selected(old('mode_calcul', isset($r) ? $r->mode_calcul->value : 'fixe') === $mc->value)>{{ $mc->libelle() }}</option>
        @endforeach
    </x-form.select>
    <x-form.select label="Filiale" name="filiale_id" :placeholder="$estGroupe ? 'Commune au groupe' : false">
        @foreach ($filiales as $f)
            <option value="{{ $f->id }}" @selected(old('filiale_id', $r->filiale_id ?? '') == $f->id)>{{ $f->nom }}</option>
        @endforeach
    </x-form.select>

    <x-form.input label="Montant fixe" name="montant" type="number" step="0.01" min="0" :value="$r->montant ?? null" hint="Si mode = fixe" />
    <x-form.input label="Taux (%)" name="taux" type="number" step="0.0001" min="0" max="100" :value="$r->taux ?? null" hint="Si mode = pourcentage" />
    <x-form.select label="Base de calcul" name="base_calcul" placeholder="—">
        <option value="salaire_base" @selected(old('base_calcul', $r->base_calcul ?? '') === 'salaire_base')>Salaire de base</option>
        <option value="brut" @selected(old('base_calcul', $r->base_calcul ?? '') === 'brut')>Salaire brut</option>
    </x-form.select>

    <x-form.input label="Ordre d'affichage" name="ordre" type="number" min="0" :value="$r->ordre ?? 0" />
    <div class="flex items-end gap-4 sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm text-slatetext">
            <input type="checkbox" name="imposable" value="1" @checked(old('imposable', $r->imposable ?? true)) class="rounded border-mist text-koanda focus:ring-koanda"> Imposable
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slatetext">
            <input type="checkbox" name="actif" value="1" @checked(old('actif', $r->actif ?? true)) class="rounded border-mist text-koanda focus:ring-koanda"> Active
        </label>
    </div>
</x-form.section>
