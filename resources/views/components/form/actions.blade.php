@props(['cancel' => null, 'label' => 'Enregistrer'])
<div class="mt-5 flex flex-wrap items-center justify-end gap-3">
    @if ($cancel)
        <a href="{{ $cancel }}" class="inline-flex items-center gap-2 rounded-lg border border-mist bg-white px-4 py-2.5 text-sm font-medium text-slatetext shadow-sm transition hover:bg-mineral">
            <x-icon name="x" class="h-4 w-4" /> Annuler
        </a>
    @endif
    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-koanda px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">
        <x-icon name="check" class="h-4 w-4" /> {{ $label }}
    </button>
</div>
