@props(['title', 'subtitle' => null])
<div class="flex items-center justify-between gap-4 bg-gradient-to-r from-forest-soft via-koanda-dark to-koanda px-6 py-5">
    <div>
        <h2 class="font-display text-lg font-bold text-white">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-white/80">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="hidden items-center gap-2 text-white sm:flex">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 font-display text-base font-extrabold">K</span>
        <div class="leading-tight">
            <p class="font-display text-sm font-bold tracking-wide">KOANDA</p>
            <p class="text-[10px] uppercase tracking-[0.2em] text-white/70">Groupe</p>
        </div>
    </div>
</div>
