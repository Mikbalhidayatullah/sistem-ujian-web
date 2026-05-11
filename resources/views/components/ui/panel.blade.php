@props([
    'title' => null,
    'eyebrow' => null,
    'description' => null,
    'aside' => null,
    'class' => '',
])

<section {{ $attributes->class(['rounded-[1.75rem] border border-white/10 bg-[linear-gradient(180deg,rgba(15,23,42,0.9),rgba(15,23,42,0.72))] p-5 shadow-[0_28px_80px_rgba(2,6,23,0.28)] backdrop-blur sm:rounded-[2rem] sm:p-6', $class]) }}>
    @if ($title || $eyebrow || $description || $aside)
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                @if ($eyebrow)
                    <p class="text-xs uppercase tracking-[0.32em] text-cyan-200/75">{{ $eyebrow }}</p>
                @endif

                @if ($title)
                    <h2 class="mt-2 text-xl font-black text-white sm:text-2xl">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-300/90">{{ $description }}</p>
                @endif
            </div>

            @if ($aside)
                <div>
                    {{ $aside }}
                </div>
            @endif
        </div>
    @endif

    @if (trim($slot))
        <div class="{{ $title || $eyebrow || $description || $aside ? 'mt-5' : '' }}">
            {{ $slot }}
        </div>
    @endif
</section>
