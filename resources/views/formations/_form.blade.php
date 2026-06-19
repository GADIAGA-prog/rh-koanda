@php $f = $formation ?? null; @endphp
<x-form.card title="Formation" subtitle="Intitulé, organisme, dates et coût">
    <x-form.section number="1" title="Détail de la formation" icon="academic">
        <x-form.input label="Intitulé" name="intitule" required :value="$f->intitule ?? null" col="sm:col-span-2" />
        <x-form.select label="Filiale" name="filiale_id" required placeholder="Choisir…">
            @foreach ($filiales as $fil)
                <option value="{{ $fil->id }}" @selected(old('filiale_id', $f->filiale_id ?? '') == $fil->id)>{{ $fil->nom }}</option>
            @endforeach
        </x-form.select>
        <x-form.select label="Statut" name="statut" required :placeholder="false">
            @foreach (\App\Models\Enums\StatutFormation::cases() as $s)
                <option value="{{ $s->value }}" @selected(old('statut', isset($f) ? $f->statut->value : 'planifiee') === $s->value)>{{ $s->libelle() }}</option>
            @endforeach
        </x-form.select>

        <x-form.input label="Organisme" name="organisme" :value="$f->organisme ?? null" col="sm:col-span-2" />
        <x-form.input label="Date de début" name="date_debut" type="date" :value="optional($f->date_debut ?? null)->format('Y-m-d')" />
        <x-form.input label="Date de fin" name="date_fin" type="date" :value="optional($f->date_fin ?? null)->format('Y-m-d')" />

        <x-form.input label="Coût" name="cout" type="number" step="0.01" min="0" :value="$f->cout ?? 0" />
        <x-form.select label="Devise" name="devise" required :placeholder="false">
            @foreach (['XOF' => 'XOF', 'EUR' => 'EUR', 'USD' => 'USD'] as $code => $lib)
                <option value="{{ $code }}" @selected(old('devise', $f->devise ?? 'XOF') === $code)>{{ $lib }}</option>
            @endforeach
        </x-form.select>
        <x-form.textarea label="Objectif" name="objectif" :value="$f->objectif ?? null" col="sm:col-span-2 lg:col-span-4" />
    </x-form.section>
</x-form.card>
