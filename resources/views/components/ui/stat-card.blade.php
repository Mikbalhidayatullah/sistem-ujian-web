@props([
    'label',
    'value',
    'note' => null,
    'tone' => 'cyan',
])

@php
    $tones = [
        'cyan' => 'border-cyan-300/15 bg-cyan-400/10 text-cyan-100/80',
        'emerald' => 'border-emerald-300/15 bg-emerald-400/10 text-emerald-100/80',
        'amber' => 'border-amber-300/15 bg-amber-400/10 text-amber-100/80',
        'fuchsia' => 'border-fuchsia-300/15 bg-fuchsia-400/10 text-fuchsia-100/80',
        'sky' => 'border-sky-300/15 bg-sky-400/10 text-sky-100/80',
        'slate' => 'border-slate-300/15 bg-slate-400/10 text-slate-100/80',
    ];
    $toneClass = $tones[$tone] ?? $tones['cyan'];
@endphp

<article {{ $attributes->class(["rounded-[1.75rem] border p-5 shadow-[0_20px_55px_rgba(2,6,23,0.2)] backdrop-blur {$toneClass}"]) }}>
    <p class="text-xs uppercase tracking-[0.26em]">{{ $label }}</p>
    <p class="mt-3 text-3xl font-black text-white">{{ $value }}</p>
    @if ($note)
        <p class="mt-3 text-xs leading-6">{{ $note }}</p>
    @endif
</article>
