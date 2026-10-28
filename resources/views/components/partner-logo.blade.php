@props(['size' => 24])

{{-- Ikon Pasar Jaya: daun hijau (dengan bentuk "p") dan daun oranye. --}}
<svg {{ $attributes->merge(['class' => 'partner-logo']) }} viewBox="6 7 89 96" width="{{ $size }}" height="{{ round($size * 96 / 89) }}" aria-hidden="true" focusable="false">
    <path fill="#00883e" d="M72.3 18.1C69 40 62 62 50 76C45 81 40 82.5 32 84.5C24 86 18 86.5 16.2 88.2C19 74 25 64 41.5 59.8C46 58.5 47.8 54.5 47.8 51.5L48.4 40.2C35 45 21 64 15.5 80C13.5 86 13 94 12.8 98C11.5 100 10 101.2 9 102C6.5 85 8 62 21 45C28 36 35 32 40 30C50 26 62 22 72.3 18.1Z"/>
    <path fill="#f7971e" d="M90.5 8.5C93 25 94 42 88 52C80 66 69 74 50 79C56 73 60 67 63.6 60C74 54 78 43 76.8 30.6L72.4 33L74.7 17.2C80 15 86 12 90.5 8.5Z"/>
</svg>
