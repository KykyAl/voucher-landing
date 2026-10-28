@props(['size' => 32])

{{-- Logo "CP" Connect Pro: huruf C hitam & P biru, tebal dan miring. --}}
<svg {{ $attributes->merge(['class' => 'brand-logo']) }} viewBox="7 5 57 30" width="{{ $size }}" height="{{ round($size * 30 / 57) }}" aria-hidden="true" focusable="false">
    <g transform="translate(9 0) skewX(-14)">
        <path d="M26.28 13.89A9.5 9.5 0 1 0 26.28 26.11" fill="none" stroke="#111" stroke-width="8"/>
        <rect x="31" y="6.5" width="8" height="27" fill="#1e8fe1"/>
        <path d="M35 10.5H46A6.5 6.5 0 0 1 46 23.5H35" fill="none" stroke="#1e8fe1" stroke-width="8"/>
    </g>
</svg>
