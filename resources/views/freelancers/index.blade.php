<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Talent marketplace</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Discover Freelancers</h2>
            <p class="mt-1 text-sm text-slate-500">Explore skills, rates, and verified project reviews.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <form method="GET" class="wv-card flex gap-3 p-4">
            <input name="q" value="{{ request('q') }}" class="wv-input flex-1" placeholder="Search name or skills">
            <x-primary-button>Search</x-primary-button>
        </form>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($freelancers as $freelancer)
                <article class="wv-card wv-card-hover p-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gradient text-lg font-bold text-white">{{ strtoupper(substr($freelancer->name, 0, 1)) }}</span>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">{{ $freelancer->name }}</h3>
                            <p class="text-sm text-amber-500">★ {{ number_format($freelancer->received_reviews_avg_rating ?? 0, 1) }} <span class="text-slate-400">({{ $freelancer->received_reviews_count }})</span></p>
                        </div>
                    </div>
                    <p class="mt-4 line-clamp-3 text-sm text-slate-500">{{ $freelancer->profile?->bio }}</p>
                    <p class="mt-4 text-sm font-medium text-brand-700">{{ $freelancer->profile?->skills }}</p>
                    <p class="mt-4 font-display text-xl font-bold text-slate-900">${{ number_format((float) $freelancer->profile?->hourly_rate, 2) }}<span class="text-sm font-normal text-slate-400">/hr</span></p>
                </article>
            @endforeach
        </div>
        {{ $freelancers->links() }}
    </div>
</x-app-layout>
