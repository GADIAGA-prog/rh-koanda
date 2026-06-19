@props([
    'label',
    'name',
    'required' => false,
    'placeholder' => 'Sélectionner',
    'col' => null,
    'hint' => null,
])
<div class="{{ $col }}">
    <label for="{{ $name }}" class="block text-sm font-medium text-slatetext">
        {{ $label }}@if ($required) <span class="text-rose-500">*</span>@endif
    </label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'mt-1.5 w-full rounded-lg border border-mist bg-white px-3 py-2.5 text-sm text-forest shadow-sm focus:border-koanda focus:outline-none focus:ring-1 focus:ring-koanda ' . ($errors->has($name) ? 'border-rose-400' : '')]) }}
    >
        @if ($placeholder !== false)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
