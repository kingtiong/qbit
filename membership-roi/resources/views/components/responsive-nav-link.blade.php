@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-emerald-400 text-start text-base font-semibold text-emerald-200 bg-white/5 focus:outline-none focus:text-emerald-100 focus:bg-white/10 focus:border-emerald-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-200 hover:text-white hover:bg-white/5 hover:border-white/20 focus:outline-none focus:text-white focus:bg-white/10 focus:border-white/20 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
