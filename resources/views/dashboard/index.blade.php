@extends('layouts.landing')
@section('title', 'Dashboard Penjualan | Connect Pro')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/voucher-dashboard.css') }}?v={{ hash_file('crc32b', public_path('css/voucher-dashboard.css')) }}">
@endpush
@section('content')
@php
    use App\Services\VoucherCatalog;

    $preview = $preview ?? false;
    $metrics = [
        ['Total transaksi', number_format((int) $totals->transactions, 0, ',', '.'), 'transaksi'],
        ['Voucher terjual', number_format((int) $totals->units, 0, ',', '.'), 'voucher'],
        ['Pendapatan', VoucherCatalog::formatRupiah((int) $totals->revenue), 'pendapatan'],
    ];
@endphp
<div class="sales-dashboard">
    <header class="sales-top">
        <div class="sales-wrap sales-top-inner">
            <a href="{{ config('voucher.static_preview') ? asset('index.html') : route('voucher.landing') }}" class="sales-brand">
                <span class="sales-brand-mark"><x-brand-logo :size="24" /></span>
                <span>Connect Pro</span>
            </a>
            <div class="sales-actions">
                @if ($preview)
                    <span class="sales-preview"><span aria-hidden="true"></span> Data contoh</span>
                @else
                    <span class="sales-user">{{ auth()->user()->name }}</span>
                @endif
                <a class="sales-button" href="{{ config('voucher.static_preview') ? asset('index.html') : route('voucher.landing') }}">Katalog <span aria-hidden="true">↗</span></a>
                @unless ($preview)
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="sales-button" type="submit">Keluar</button>
                    </form>
                @endunless
            </div>
        </div>
    </header>

    <main class="sales-wrap sales-main" id="ringkasan">
        @if (session('status'))
            <p class="alert alert-success" role="status">{{ session('status') }}</p>
        @endif

        <div class="sales-heading">
            <div>
                <h1>Dashboard Penjualan</h1>
                <p>Ringkasan semua pembelian voucher yang sudah lunas.</p>
            </div>
            @unless ($preview)
                <a class="sales-button sales-button-primary" href="#catat" onclick="document.getElementById('catat').open = true">+ Catat pembelian</a>
            @endunless
        </div>

        <section class="sales-metrics" aria-label="Ringkasan penjualan">
            @foreach ($metrics as [$label, $value, $type])
                <article class="sales-metric sales-metric-{{ $type }}">
                    <div class="sales-metric-top">
                        <h2>{{ $label }}</h2>
                        <span class="sales-metric-icon" aria-hidden="true">
                            @switch($type)
                                @case('transaksi') <i class="sales-icon-receipt"></i> @break
                                @case('voucher') <i class="sales-icon-ticket"></i> @break
                                @case('pendapatan') Rp @break
                            @endswitch
                        </span>
                    </div>
                    <p class="sales-value">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section class="sales-panel" aria-labelledby="sales-chart-title">
            <div class="sales-panel-head">
                <div>
                    <h2 class="sales-panel-title" id="sales-chart-title">Tren transaksi</h2>
                    <p>Jumlah pembelian lunas per hari</p>
                </div>
                <span class="sales-period">14 hari terakhir · WIB</span>
            </div>
            <div class="sales-chart-scroll" tabindex="0" role="region" aria-label="Grafik transaksi harian">
                <div class="sales-chart">
                    <div class="sales-axis" aria-hidden="true">
                        <span>{{ $chartMax }}</span>
                        <span>0</span>
                    </div>
                    <ol class="sales-bars" aria-label="Jumlah transaksi per tanggal">
                        @foreach ($days as $day)
                            <li @class(['sales-bar-day', 'sales-bar-today' => $loop->last])>
                                <div class="sales-bar-track">
                                    <div @class(['sales-bar', 'sales-bar-empty' => $day['count'] === 0]) style="--bar-height: {{ $day['count'] / $chartMax * 100 }}%">
                                        <span class="sales-bar-count">{{ $day['count'] }}</span>
                                    </div>
                                </div>
                                <span class="sales-bar-label">{{ $day['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        <section class="sales-panel sales-table-panel" aria-labelledby="sales-packages-title">
            <div class="sales-panel-head">
                <div>
                    <h2 class="sales-panel-title" id="sales-packages-title">Penjualan per paket</h2>
                    <p>Urutan berdasarkan jumlah voucher terjual</p>
                </div>
            </div>
            <div class="table-responsive" tabindex="0" role="region" aria-labelledby="sales-packages-title">
                <table class="sales-table">
                    <thead>
                        <tr><th scope="col">Paket</th><th scope="col">Transaksi</th><th scope="col">Voucher terjual</th><th scope="col">Pendapatan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $package)
                            <tr>
                                <th scope="row">{{ $package->voucher_name }}</th>
                                <td>{{ $package->transactions }}</td>
                                <td>{{ $package->units }}</td>
                                <td class="sales-money">{{ VoucherCatalog::formatRupiah((int) $package->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="sales-empty">Belum ada penjualan yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sales-panel sales-table-panel" aria-labelledby="sales-history-title">
            <div class="sales-panel-head">
                <div>
                    <h2 class="sales-panel-title" id="sales-history-title">Riwayat pembelian</h2>
                    <p>Transaksi lunas terbaru</p>
                </div>
            </div>
            <div class="table-responsive" tabindex="0" role="region" aria-labelledby="sales-history-title">
                <table class="sales-table sales-history">
                    <thead>
                        <tr><th scope="col">Referensi</th><th scope="col">Tanggal lunas</th><th scope="col">Paket</th><th scope="col">Jumlah</th><th scope="col">Total dibayar</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <th scope="row"><span class="sales-reference">{{ $sale->reference }}</span></th>
                                <td>{{ \Carbon\CarbonImmutable::parse($sale->paid_on)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $sale->voucher_name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td class="sales-money">{{ VoucherCatalog::formatRupiah($sale->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="sales-empty">Belum ada pembelian lunas yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (! $preview && $sales->hasPages())
                <div class="sales-pagination">{{ $sales->links('pagination::simple-bootstrap-5') }}</div>
            @endif
        </section>
        @unless ($preview)
            <details id="catat" class="sales-panel sales-entry" @if ($errors->any()) open @endif>
                <summary class="sales-panel-title">Catat pembelian lunas</summary>
                <p class="text-secondary mt-3">Isi setelah pembayaran dikonfirmasi. Gunakan nomor referensi yang unik untuk setiap transaksi.</p>
                @if ($errors->any())
                    <ul class="alert alert-danger ps-5" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <form method="post" action="{{ route('dashboard.sales.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Referensi transaksi</label>
                        <input id="reference" name="reference" class="form-control" value="{{ old('reference') }}" maxlength="80" required>
                    </div>
                    <div class="col-md-6">
                        <label for="voucher_code" class="form-label">Paket voucher</label>
                        <select id="voucher_code" name="voucher_code" class="form-select" required>
                            <option value="">Pilih paket</option>
                            @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher['code'] }}" @selected(old('voucher_code') === $voucher['code'])>{{ $voucher['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Jumlah voucher</label>
                        <input id="quantity" name="quantity" type="number" min="1" max="10000" class="form-control" value="{{ old('quantity', 1) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="total" class="form-label">Total dibayar (Rp)</label>
                        <input id="total" name="total" type="number" min="1" max="10000000000" class="form-control" value="{{ old('total') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="paid_on" class="form-label">Tanggal lunas</label>
                        <input id="paid_on" name="paid_on" type="date" max="{{ $today }}" class="form-control" value="{{ old('paid_on', $today) }}" required>
                    </div>
                    <div class="col-12"><button class="sales-button sales-button-primary" type="submit">Simpan pembelian</button></div>
                </form>
            </details>
        @endunless
    </main>
</div>
@endsection
