@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'aside' => null,
    'class' => '',
])

<div {{ $attributes->class(["flex flex-wrap items-start justify-between gap-3 {$class}"]) }}>
    <div>
        @if ($eyebrow)
            <p class="dashboard-kicker">{{ $eyebrow }}</p>
        @endif

        @if ($title)
            <h2 class="dashboard-section-title mt-2">{{ $title }}</h2>
        @endif

        @if ($description)
            <p class="dashboard-copy mt-3">{{ $description }}</p>
        @endif
    </div>

    @if ($aside)
        <div>
            {{ $aside }}
        </div>
    @endif
</div>
