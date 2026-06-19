@php $a = $absence ?? null; @endphp
<x-form.card title="Absence" subtitle="Période, motif et pièce justificative">
    <x-form.section number="1" title="Détail de l'absence" icon="calendar">
        @if ($a)
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slatetext">Employé</label>
                <p class="mt-1.5 rounded-lg border border-mist bg-mineral px-3 py-2.5 text-sm text-forest">{{ $a->employe->nom_complet }}</p>
                <input type="hidden" name="employe_id" value="{{ $a->employe_id }}">
            </div>
        @else
            <x-form.select label="Employé" name="employe_id" required placeholder="Choisir…" col="sm:col-span-2">
                @foreach ($employes as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employe_id') == $emp->id)>{{ $emp->nom_complet }} — {{ $emp->matricule }}</option>
                @endforeach
            </x-form.select>
        @endif

        <x-form.input label="Date de début" name="date_debut" type="date" required :value="optional($a->date_debut ?? null)->format('Y-m-d')" />
        <x-form.input label="Date de fin" name="date_fin" type="date" required :value="optional($a->date_fin ?? null)->format('Y-m-d')" />
        <x-form.input label="Motif" name="motif" :value="$a->motif ?? null" col="sm:col-span-2 lg:col-span-4" />

        <div class="sm:col-span-2">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-mist px-3 py-2.5 text-sm has-[:checked]:border-koanda has-[:checked]:bg-koanda-light">
                <input type="checkbox" name="justifiee" value="1" @checked(old('justifiee', $a->justifiee ?? false)) class="rounded border-mist text-koanda focus:ring-koanda">
                <span class="font-medium text-slatetext">Absence justifiée</span>
            </label>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slatetext">Pièce justificative <span class="font-normal text-slate-400">(PDF/JPG/PNG, 5 Mo max)</span></label>
            <input type="file" name="justificatif" class="mt-1.5 w-full rounded-lg border border-mist bg-white px-3 py-2 text-sm text-forest file:mr-3 file:rounded file:border-0 file:bg-koanda-light file:px-3 file:py-1 file:text-koanda-dark">
            @if ($a && $a->justificatif)
                <p class="mt-1 text-xs text-slate-400">Pièce actuelle : <a href="{{ route('absences.justificatif', $a) }}" class="text-koanda-dark hover:underline">télécharger</a></p>
            @endif
            @error('justificatif')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </x-form.section>
</x-form.card>
