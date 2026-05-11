@props([
    'tone' => 'default',
    'class' => '',
])

@php
    $tones = [
        'default' => 'dashboard-pill',
        'success' => 'dashboard-pill border-emerald-200 bg-emerald-50 text-emerald-700',
        'danger' => 'dashboard-pill border-rose-200 bg-rose-50 text-rose-700',
        'warning' => 'dashboard-pill border-amber-200 bg-amber-50 text-amber-700',
        'slate' => 'dashboard-pill border-slate-200 bg-slate-100 text-slate-600',
        'info' => 'dashboard-pill border-sky-200 bg-sky-50 text-sky-700',
    ];

    $toneClass = $tones[$tone] ?? $tones['default'];
@endphp

<span {{ $attributes->class([$toneClass, $class]) }}>
    {{ $slot }}
</span>
