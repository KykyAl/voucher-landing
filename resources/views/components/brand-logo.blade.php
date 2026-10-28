@props(['size' => 32])

{{--
    Logo "S" SO NET: dua lengkung tiga lapis (biru-kuning-biru) yang saling
    menyambung di tengah, masing-masing dengan titik target di pusatnya.
--}}
<svg {{ $attributes->merge(['class' => 'brand-logo']) }} viewBox="11 2 38 64" width="{{ $size }}" height="{{ round($size * 64 / 38) }}" aria-hidden="true" focusable="false">
    <g fill="none" stroke-width="4.2">
        <path d="M43.02 10.07A17 17 0 1 0 30 38A9 9 0 1 1 23.11 52.79" stroke="#1d9bd7"/>
        <path d="M36.89 15.21A9 9 0 1 0 30 30A17 17 0 1 1 16.98 57.93" stroke="#1d9bd7"/>
        <path d="M39.96 12.64A13 13 0 1 0 30 34A13 13 0 1 1 20.04 55.36" stroke="#f6c21c"/>
    </g>
    <circle cx="30" cy="21" r="4.6" fill="#1d9bd7"/>
    <circle cx="30" cy="21" r="2.1" fill="#1f5fa8"/>
    <circle cx="30" cy="47" r="4.6" fill="#1d9bd7"/>
    <circle cx="30" cy="47" r="2.1" fill="#1f5fa8"/>
</svg>
