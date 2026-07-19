@props(['withText' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 shrink-0">
        <defs>
            <linearGradient id="wvLogoGrad" x1="6" y1="4" x2="42" y2="44" gradientUnits="userSpaceOnUse">
                <stop stop-color="#6366F1" />
                <stop offset="0.55" stop-color="#7C3AED" />
                <stop offset="1" stop-color="#4F46E5" />
            </linearGradient>
        </defs>
        <path d="M24 3.5 40.4 13v22L24 44.5 7.6 35V13L24 3.5Z" fill="url(#wvLogoGrad)" />
        <path d="M24 3.5 40.4 13v22L24 44.5 7.6 35V13L24 3.5Z" fill="white" fill-opacity="0.06" />
        <path d="M16.5 24.5l5 5 10-10" stroke="white" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    @if ($withText)
        <span class="font-display text-lg font-extrabold tracking-tight text-slate-900">Work<span class="text-gradient">Vault</span></span>
    @endif
</span>
