@props([
    'label',
    'name',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'rows' => 3,
    'col' => 'sm:col-span-2 lg:col-span-4',
    'hint' => null,
])
<div class="{{ $col }}">
    <label for="{{ $name }}" class="block text-sm font-medium text-slatetext">
        {{ $label }}@if ($required) <span class="text-rose-500">*</span>@endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'mt-1.5 w-full rounded-lg border border-mist bg-white px-3 py-2.5 text-sm text-forest shadow-sm placeholder:text-slate-400 focus:border-koanda focus:outline-none focus:ring-1 focus:ring-koanda ' . ($errors->has($name) ? 'border-rose-400' : '')]) }}
    >{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
