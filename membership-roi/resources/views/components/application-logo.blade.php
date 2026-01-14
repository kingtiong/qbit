@php
    // Prefer an uploaded image in public/images (or public/image), fallback to old SVG.
    $logoCandidates = [
        'images/logo.png',
        'image/logo.png',
        'images/logo.webp',
        'image/logo.webp',
        'images/logo.jpg',
        'image/logo.jpg',
        'images/logo.jpeg',
        'image/logo.jpeg',
        'images/logo.svg',
        'image/logo.svg',
    ];
    $logoPath = null;
    foreach ($logoCandidates as $p) {
        if (file_exists(public_path($p))) {
            $logoPath = $p;
            break;
        }
    }
@endphp

@if ($logoPath)
    @php
        // Cache-bust when the file is replaced but keeps the same name.
        $logoVersion = @filemtime(public_path($logoPath)) ?: null;
        $logoUrl = asset($logoPath) . ($logoVersion ? ('?v=' . $logoVersion) : '');
    @endphp
    <img
        src="{{ $logoUrl }}"
        alt="{{ config('app.name', 'Logo') }}"
        {{ $attributes->merge(['class' => 'block object-contain object-center']) }}
    />
@else
    <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
        <rect x="6" y="6" width="108" height="108" rx="20" fill="currentColor" opacity="0.12" />
        <text
            x="60"
            y="74"
            text-anchor="middle"
            font-family="Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif"
            font-size="52"
            font-weight="800"
            letter-spacing="1"
            fill="currentColor"
        >GN</text>
    </svg>
@endif
