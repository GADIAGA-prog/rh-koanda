@props(['number' => null, 'title', 'icon' => 'user', 'cols' => 4])
@php
    $grid = match ((int) $cols) {
        1 => 'sm:grid-cols-1',
        2 => 'sm:grid-cols-2',
        3 => 'sm:grid-cols-2 lg:grid-cols-3',
        default => 'sm:grid-cols-2 lg:grid-cols-4',
    };
@endphp
<section class="px-6 py-6">
    <div class="flex items-center gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-koanda text-white shadow-sm">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
        <h3 class="font-display text-base font-bold text-forest">
            @if ($number)<span class="text-koanda-dark">{{ $number }}.</span> @endif{{ $title }}
        </h3>
    </div>
    <div class="mt-5 grid grid-cols-1 gap-x-5 gap-y-5 {{ $grid }}">
        {{ $slot }}
    </div>
</section>
