@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-950 text-slate-100 border-white/10 focus:border-emerald-400 focus:ring-emerald-400 rounded-md shadow-sm placeholder:text-slate-500']) }}>
