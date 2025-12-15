@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-2 rounded-xl text-start text-base font-medium text-slate-900 bg-emerald-50 ring-1 ring-emerald-900/10 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition'
            : 'block w-full px-4 py-2 rounded-xl text-start text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
