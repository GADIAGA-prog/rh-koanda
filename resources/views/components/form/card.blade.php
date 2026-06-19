@props(['title', 'subtitle' => null])
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-mist bg-white shadow-sm']) }}>
    <x-form.banner :title="$title" :subtitle="$subtitle" />

    <div class="divide-y divide-mist">
        {{ $slot }}
    </div>
</div>
