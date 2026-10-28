@props(['name'])

{{-- Tulisan logo: kata pertama biru, sisanya oranye ("Connect" + "Pro"). --}}
@php([$firstWord, $restWords] = array_pad(explode(' ', $name, 2), 2, ''))
<span {{ $attributes->merge(['class' => 'brand-wordmark']) }}><span class="is-first">{{ $firstWord }}</span><span class="is-rest">{{ $restWords }}</span></span>
