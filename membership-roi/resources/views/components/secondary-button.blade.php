<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-neutral']) }}>
    {{ $slot }}
</button>
