@props([
    'height' => '28rem',
    'class' => '',
    'contentClass' => '',
    'contentAttributes' => [],
])

<div {{ $attributes->class(['overflow-hidden rounded-[1.5rem] border border-slate-200/80', $class]) }}>
    <div
        class="{{ $contentClass }}"
        style="height: {{ $height }}; overflow-y: auto;"
        @foreach ($contentAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
    >
        {{ $slot }}
    </div>
</div>
