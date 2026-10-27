@extends('layouts.landing')

@php($rupiah = \App\Services\VoucherCatalog::formatRupiah(...))

@section('title', $brand['name'].' — Beli Voucher')
@section('description', $brand['description'])

@section('content')
<header class="hero text-white">
    <div class="container py-4 py-md-5">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="brand-mark" aria-hidden="true">{{ mb_substr($brand['name'], 0, 1) }}</span>
            <span class="fw-bold fs-5">{{ $brand['name'] }}</span>
        </div>
        <h1 class="display-6 fw-bold mb-2">{{ $brand['tagline'] }}</h1>
        <p class="lead mb-4 opacity-75">{{ $brand['description'] }}</p>
        <a href="#pilih-voucher" class="btn btn-light btn-lg fw-semibold px-4">Lihat Voucher</a>
    </div>
</header>

<main class="pb-summary">
    <section id="pilih-voucher" class="container py-4 py-md-5" aria-labelledby="judul-voucher">
        <h2 id="judul-voucher" class="h4 fw-bold mb-1">Pilih Voucher</h2>
        <p class="text-secondary mb-4">Ketuk salah satu voucher di bawah ini, lalu tentukan jumlahnya.</p>

        <form id="voucher-form" novalidate>
            <fieldset>
                <legend class="visually-hidden">Daftar voucher</legend>
                <div class="row g-3">
                    @foreach ($vouchers as $voucher)
                        @php($inputId = 'voucher-'.$loop->index)
                        <div class="col-12 col-md-6 col-lg-3">
                            <input type="radio"
                                   class="btn-check voucher-option"
                                   name="voucher"
                                   id="{{ $inputId }}"
                                   value="{{ $voucher['code'] }}"
                                   data-name="{{ $voucher['name'] }}"
                                   data-price="{{ $voucher['price'] }}"
                                   data-validity="{{ $voucher['validity_days'] }}"
                                   @disabled(! $voucher['is_available'])>
                            <label for="{{ $inputId }}" class="card voucher-card h-100 {{ $voucher['is_available'] ? '' : 'is-unavailable' }}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <span class="badge text-bg-light border">{{ $voucher['code'] }}</span>
                                        @if (! $voucher['is_available'])
                                            <span class="badge text-bg-secondary">Stok habis</span>
                                        @elseif ($voucher['is_featured'])
                                            <span class="badge text-bg-warning">Terlaris</span>
                                        @endif
                                    </div>
                                    <h3 class="h6 fw-bold mb-1">{{ $voucher['name'] }}</h3>
                                    <p class="small text-secondary mb-3">{{ $voucher['description'] }}</p>

                                    <div class="mt-auto">
                                        @if ($voucher['discount_percent'] > 0)
                                            <div class="small">
                                                <span class="text-secondary text-decoration-line-through">{{ $rupiah($voucher['face_value']) }}</span>
                                                <span class="text-success fw-semibold ms-1">Hemat {{ $voucher['discount_percent'] }}%</span>
                                            </div>
                                        @endif
                                        <div class="voucher-price">{{ $rupiah($voucher['price']) }}</div>
                                        <div class="small text-secondary mt-1">Berlaku {{ $voucher['validity_days'] }} hari setelah pembelian</div>
                                    </div>
                                </div>
                                <span class="voucher-check" aria-hidden="true">✓</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div>
                        <label for="quantity" class="form-label fw-semibold mb-0">Jumlah voucher</label>
                        <div class="small text-secondary">Maksimal {{ $maxQuantity }} voucher per pembelian</div>
                    </div>
                    <div class="input-group quantity-group" role="group" aria-label="Atur jumlah voucher">
                        <button type="button" class="btn btn-outline-secondary" data-qty-step="-1" aria-label="Kurangi jumlah">−</button>
                        <input type="number" class="form-control text-center" id="quantity" name="quantity"
                               value="1" min="1" max="{{ $maxQuantity }}" inputmode="numeric">
                        <button type="button" class="btn btn-outline-secondary" data-qty-step="1" aria-label="Tambah jumlah">+</button>
                    </div>
                </div>
            </div>
        </form>
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
        &copy; {{ now()->year }} {{ $brand['name'] }}
    </footer>
</main>

{{-- Ringkasan pilihan, menempel di bawah layar --}}
<div class="summary-bar shadow-lg" aria-live="polite">
    <div class="container d-flex align-items-center justify-content-between gap-3 py-3">
        <div class="min-w-0">
            <div id="summary-empty" class="small text-secondary">Belum ada voucher dipilih</div>
            <div id="summary-filled" class="d-none">
                <div class="small text-secondary text-truncate"><span id="summary-name"></span> × <span id="summary-qty"></span></div>
                <div class="fw-bold fs-5" id="summary-total"></div>
            </div>
        </div>
        <button type="button" id="btn-continue" class="btn btn-success btn-lg fw-semibold flex-shrink-0" disabled
                data-bs-toggle="modal" data-bs-target="#summary-modal">
            Lanjutkan
        </button>
    </div>
</div>

<div class="modal fade" id="summary-modal" tabindex="-1" aria-labelledby="summary-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="summary-modal-title">Ringkasan Pembelian</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0 summary-list">
                    <dt class="col-6 fw-normal text-secondary">Voucher</dt>
                    <dd class="col-6 text-end fw-semibold" id="modal-name"></dd>
                    <dt class="col-6 fw-normal text-secondary">Masa berlaku</dt>
                    <dd class="col-6 text-end" id="modal-validity"></dd>
                    <dt class="col-6 fw-normal text-secondary">Harga satuan</dt>
                    <dd class="col-6 text-end" id="modal-price"></dd>
                    <dt class="col-6 fw-normal text-secondary">Jumlah</dt>
                    <dd class="col-6 text-end" id="modal-qty"></dd>
                    <dt class="col-6 border-top pt-2">Total</dt>
                    <dd class="col-6 text-end border-top pt-2 fw-bold fs-5" id="modal-total"></dd>
                </dl>

                <div class="alert alert-info d-flex gap-2 mt-3 mb-0" role="status">
                    <span aria-hidden="true">ℹ️</span>
                    <div>
                        <strong>Pembayaran belum tersedia.</strong>
                        Fitur pembayaran online sedang kami siapkan. Silakan kembali lagi nanti.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Ubah Pilihan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/voucher-landing.js') }}"></script>
@endpush
