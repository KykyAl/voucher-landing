@props(['size' => 32])

{{-- Ikon Connect Pro: tiga bola (oranye & biru) terhubung, dengan lengkung orbit. --}}
<svg {{ $attributes->merge(['class' => 'brand-logo']) }} viewBox="1 1 61 56" width="{{ $size }}" height="{{ round($size * 56 / 61) }}" aria-hidden="true" focusable="false">
    <path d="M3 46Q20 64 52 43Q22 53 3 46Z" fill="#ff8a00"/>
    <path d="M35 25Q43 6 61 8Q46 12 40 25Z" fill="#1478e6"/>
    <path d="M40.5 24Q45 15 56 12.5Q47 17 43.5 24Z" fill="#7cc4ff"/>

    <g stroke-linecap="round">
        <line x1="42" y1="9" x2="11" y2="25" stroke="#ff8a00" stroke-width="4.5"/>
        <line x1="42" y1="9" x2="26" y2="54" stroke="#ff8a00" stroke-width="5.5"/>
        <line x1="11" y1="25" x2="40" y2="42" stroke="#1478e6" stroke-width="7.5"/>
    </g>

    <circle cx="42" cy="9" r="7" fill="#ff8a00"/>
    <circle cx="40.4" cy="7.4" r="3.8" fill="#ffb347"/>
    <circle cx="43.4" cy="6.8" r="1.8" fill="#0b1f3a"/>

    <circle cx="11" cy="25" r="7.5" fill="#1478e6"/>
    <circle cx="9.3" cy="23.3" r="4" fill="#4fb0ff"/>
    <circle cx="12.5" cy="22.5" r="1.9" fill="#0b1f3a"/>

    <circle cx="40" cy="42" r="8" fill="#1478e6"/>
    <circle cx="38.2" cy="40.2" r="4.3" fill="#4fb0ff"/>
    <circle cx="41.6" cy="39.3" r="2" fill="#0b1f3a"/>
</svg>
