@props([
    'tone' => 'sky',
    'class' => '',
])

@php
    $tones = [
        'sky' => 'bg-sky-100 text-sky-600',
        'emerald' => 'bg-emerald-100 text-emerald-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'rose' => 'bg-rose-100 text-rose-600',
        'violet' => 'bg-violet-100 text-violet-600',
    ];

    $toneClass = $tones[$tone] ?? $tones['sky'];
@endphp

<span {{ $attributes->class(["grid h-12 w-12 place-items-center rounded-2xl {$toneClass} {$class}"]) }}>
    {{ $slot }}
</span>
