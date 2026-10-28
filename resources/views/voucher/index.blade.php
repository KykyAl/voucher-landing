@extends('layouts.landing')

@php($rupiah = \App\Services\VoucherCatalog::formatRupiah(...))
@php($cartIcon = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.6 12.4a1.5 1.5 0 0 0 1.5 1.1h9.2a1.5 1.5 0 0 0 1.5-1.2L21 8H6"/></svg>')

@section('title', $brand['name'].' × '.$brand['partner'].' — Beli Voucher')
@section('description', $brand['description'])

@section('content')
<header class="hero text-white">
    <div class="container py-4 py-md-5">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4">
            {{-- Logo kolaborasi: Connect Pro × Pasar Jaya --}}
            <div class="brand-lockup">
                <x-brand-logo :size="34" />
                <x-brand-wordmark :name="$brand['name']" />
                <span class="brand-x" aria-hidden="true">×</span>
                <x-partner-logo :size="24" />
                <span class="partner-wordmark">{{ $brand['partner'] }}</span>
            </div>
            <button type="button" class="cart-trigger" data-bs-toggle="offcanvas" data-bs-target="#cart" aria-label="Buka keranjang">
                {!! $cartIcon !!}
                <span class="cart-count" data-cart-count hidden></span>
            </button>
        </div>
        <h1 class="display-6 fw-bold mb-2">{{ $brand['tagline'] }}</h1>
        <p class="lead mb-4 opacity-75">{{ $brand['description'] }}</p>
        <a href="#pilih-voucher" class="btn btn-light btn-lg fw-semibold px-4">Lihat Voucher</a>
    </div>
</header>

<main class="pb-summary">
    <section id="pilih-voucher" class="container py-4 py-md-5" aria-labelledby="judul-voucher">
        <h2 id="judul-voucher" class="h4 fw-bold mb-1">Pilih Voucher</h2>
        <p class="text-secondary mb-4">Tambahkan voucher ke keranjang, maksimal {{ $maxQuantity }} voucher per pembelian.</p>

        <div class="row g-3">
            @foreach ($vouchers as $voucher)
                <div class="col-12 col-sm-6 col-xl-3">
                    <article @class([
                                 'voucher-ticket h-100',
                                 'is-featured' => $voucher['is_featured'] && $voucher['is_available'],
                                 'is-unavailable' => ! $voucher['is_available'],
                             ])
                             data-code="{{ $voucher['code'] }}"
                             data-name="{{ $voucher['name'] }}"
                             data-price="{{ $voucher['price'] }}"
                             data-face-value="{{ $voucher['face_value'] }}"
                             data-duration="{{ $voucher['duration'] }}"
                             data-available="{{ $voucher['is_available'] ? '1' : '0' }}">
                        <div class="voucher-ticket-inner">
                            {{-- Sobekan tiket: masa berlaku, seperti pita samping pada voucher cetak --}}
                            <div class="voucher-stub" aria-hidden="true">
                                <span class="voucher-stub-mark">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M2 8.5a15 15 0 0 1 20 0M5.5 12.5a10 10 0 0 1 13 0M9 16.3a5 5 0 0 1 6 0"/><circle cx="12" cy="19.5" r="1.3" fill="currentColor" stroke="none"/></svg>
                                </span>
                                <span class="voucher-stub-text">{{ $voucher['duration'] }}</span>
                            </div>

                            <div class="voucher-body">
                                <div class="voucher-head">
                                    <span class="voucher-brand">
                                        <x-brand-logo :size="22" /><x-brand-wordmark :name="$brand['name']" />
                                        <span class="brand-x" aria-hidden="true">×</span><x-partner-logo :size="15" />
                                        <span class="partner-wordmark">{{ $brand['partner'] }}</span>
                                    </span>
                                    <span class="voucher-code">{{ $voucher['code'] }}</span>
                                </div>

                                <div class="voucher-label-row">
                                    <span class="voucher-label">Harga</span>
                                    @if (! $voucher['is_available'])
                                        <span class="voucher-badge is-muted">Stok habis</span>
                                    @elseif ($voucher['is_featured'])
                                        <span class="voucher-badge is-gold">★ Terlaris</span>
                                    @endif
                                </div>
                                <span class="voucher-amount">{{ $rupiah($voucher['price']) }}</span>
                                <h3 class="voucher-name">{{ $voucher['name'] }}</h3>
                                <p class="voucher-desc">{{ $voucher['description'] }}</p>

                                <span class="voucher-divider" aria-hidden="true"></span>

                                <div class="voucher-foot">
                                    <div>
                                        <span class="voucher-label">Masa aktif</span>
                                        <span class="voucher-duration">
                                            <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1Zm0 1.5a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm-.75 2v4.06l3.02 1.8.77-1.29-2.29-1.37V4.5h-1.5Z"/></svg>
                                            {{ $voucher['duration'] }}
                                        </span>
                                    </div>
                                    @if ($voucher['discount_percent'] > 0)
                                        <div class="text-end">
                                            <span class="voucher-strike">{{ $rupiah($voucher['face_value']) }}</span>
                                            <span class="voucher-save">Hemat {{ $voucher['discount_percent'] }}%</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="voucher-action">
                                    <button type="button" class="btn voucher-add" data-cart-add value="{{ $voucher['code'] }}" @disabled(! $voucher['is_available'])>
                                        @if ($voucher['is_available'])
                                            {!! $cartIcon !!} Tambah ke keranjang
                                        @else
                                            Stok habis
                                        @endif
                                    </button>
                                    @if ($voucher['is_available'])
                                        <div class="qty-stepper" role="group" aria-label="Jumlah {{ $voucher['name'] }} di keranjang" hidden>
                                            <button type="button" data-cart-step="-1" aria-label="Kurangi jumlah">−</button>
                                            <output data-cart-qty>0</output>
                                            <button type="button" data-cart-step="1" aria-label="Tambah jumlah">+</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="voucher-check" aria-hidden="true">✓</span>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-body-tertiary" aria-labelledby="judul-cara">
        <div class="container py-4 py-md-5">
            <h2 id="judul-cara" class="h4 fw-bold mb-4">Cara Pembelian</h2>
            <ol class="row g-3 list-unstyled mb-0">
                @foreach ($steps as $step)
                    <li class="col-12 col-md-6 col-lg-3">
                        <div class="d-flex gap-3 h-100">
                            <span class="step-number" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">{{ $step['title'] }}</h3>
                                <p class="small text-secondary mb-0">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <footer class="container py-4 text-center small text-secondary">
        Butuh bantuan? Hubungi <a href="mailto:{{ $brand['support_contact'] }}">{{ $brand['support_contact'] }}</a><br>
         {{ $brand['name'] }} × {{ $brand['partner'] }}<br>
        &copy; {{ now()->year }} FAB
    </footer>
</main>

{{-- Ringkasan keranjang, menempel di bawah layar --}}
<div class="summary-bar shadow-lg" aria-live="polite">
    <div class="container d-flex align-items-center justify-content-between gap-3 py-3">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <span class="summary-icon">
                {!! $cartIcon !!}
                <span class="cart-count" data-cart-count hidden></span>
            </span>
            <div class="min-w-0">
                <div id="summary-empty" class="small text-secondary">Keranjang masih kosong</div>
                <div id="summary-filled" class="d-none">
                    <div class="small text-secondary text-truncate"><span id="summary-qty"></span> voucher<span id="summary-saving-wrap"> · hemat <span id="summary-saving"></span></span></div>
                    <div class="fw-bold fs-5" id="summary-total"></div>
                </div>
            </div>
        </div>
        <button type="button" id="btn-cart" class="btn btn-brand btn-lg fw-semibold flex-shrink-0" disabled
                data-bs-toggle="offcanvas" data-bs-target="#cart">
            Lihat Keranjang
        </button>
    </div>
</div>

<div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cart" aria-labelledby="cart-title"
     data-max-quantity="{{ $maxQuantity }}">
    <div class="offcanvas-header">
        <div>
            <h2 class="offcanvas-title h5 fw-bold mb-0" id="cart-title">Keranjang</h2>
            <div class="small text-secondary"><span id="cart-count-text">0</span> dari {{ $maxQuantity }} voucher</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>

    <div class="offcanvas-body">
        <div id="cart-empty" class="cart-empty">
            <span class="cart-empty-icon">{!! $cartIcon !!}</span>
            <h3 class="h6 fw-bold mb-1">Keranjang masih kosong</h3>
            <p class="small text-secondary mb-3">Tambahkan voucher pilihan Anda untuk mulai berbelanja.</p>
            <button type="button" class="btn btn-outline-brand fw-semibold" data-bs-dismiss="offcanvas">Pilih Voucher</button>
        </div>
        <ul id="cart-items" class="list-unstyled d-grid gap-3 mb-0"></ul>
    </div>

    <div id="cart-footer" class="cart-footer d-none">
        <p id="cart-limit" class="cart-limit d-none">Batas {{ $maxQuantity }} voucher per pembelian sudah tercapai.</p>
        <dl class="cart-totals">
            <div data-saving-row><dt>Nilai voucher</dt><dd id="cart-face-total"></dd></div>
            <div data-saving-row><dt>Hemat</dt><dd id="cart-saving" class="text-success"></dd></div>
            <div class="is-total"><dt>Total bayar</dt><dd id="cart-total"></dd></div>
        </dl>

        <div class="alert alert-info small d-flex gap-2 mb-3" role="status">
            <span aria-hidden="true">ℹ️</span>
            <div>
                <strong>Pembayaran belum tersedia.</strong>
                Fitur pembayaran tahap develop.
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" id="cart-clear" class="btn btn-outline-secondary">Kosongkan</button>
            <button type="button" class="btn btn-brand fw-semibold flex-grow-1" disabled>Bayar Sekarang</button>
        </div>
    </div>
</div>

<template id="cart-item-template">
    <li class="cart-item">
        <span class="cart-item-stub" aria-hidden="true"><span data-field="duration"></span></span>
        <div class="cart-item-body">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="min-w-0">
                    <div class="cart-item-name" data-field="name"></div>
                    <div class="small text-secondary"><span data-field="price"></span> / voucher</div>
                </div>
                <button type="button" class="cart-item-remove" data-cart-remove aria-label="Hapus dari keranjang">
                    <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M6 1.5h4a1 1 0 0 1 1 1V3h3v1.5h-1l-.7 9.1a1.5 1.5 0 0 1-1.5 1.4H5.2a1.5 1.5 0 0 1-1.5-1.4L3 4.5H2V3h3v-.5a1 1 0 0 1 1-1Zm-1.5 3 .7 9h5.6l.7-9h-7Z"/></svg>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                <div class="qty-stepper is-sm" role="group" aria-label="Atur jumlah">
                    <button type="button" data-cart-step="-1" aria-label="Kurangi jumlah">−</button>
                    <output data-cart-qty></output>
                    <button type="button" data-cart-step="1" aria-label="Tambah jumlah">+</button>
                </div>
                <div class="fw-bold" data-field="subtotal"></div>
            </div>
        </div>
    </li>
</template>
@endsection

@push('scripts')
    <script src="{{ asset('js/voucher-landing.js') }}?v={{ hash_file('crc32b', public_path('js/voucher-landing.js')) }}"></script>
@endpush
