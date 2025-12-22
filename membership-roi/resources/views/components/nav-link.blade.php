@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 py-2 rounded-xl text-sm font-medium leading-5 text-amber-50 bg-amber-500/10 ring-1 ring-amber-300/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-black transition'
            : 'inline-flex items-center px-2 py-2 rounded-xl text-sm font-medium leading-5 text-amber-50/80 hover:text-amber-50 hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-black transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
