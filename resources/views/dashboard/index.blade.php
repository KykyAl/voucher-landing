@extends('layouts.landing')
@section('title', 'Dashboard Penjualan | Connect Pro')
@section('content')
@php use App\Services\VoucherCatalog; @endphp
<header class="hero text-white">
    <div class="container py-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="mb-1 fw-semibold">Connect Pro</p>
            <h1 class="h2 fw-bold mb-0">Dashboard Penjualan</h1>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3">
            @if ($preview ?? false)
                <span class="badge text-bg-light">Preview · Data contoh</span>
            @else
                <span>{{ auth()->user()->name }}</span>
            @endif
            <a class="text-white" href="{{ route('voucher.landing') }}">Katalog</a>
            @unless ($preview ?? false)
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
            </form>
            @endunless
        </div>
    </div>
</header>

<main class="container py-4">
    @if (session('status'))
        <p class="alert alert-success" role="status">{{ session('status') }}</p>
    @endif
    <h2 class="h5 fw-bold mb-3">Ringkasan semua pembelian lunas</h2>
    <div class="row g-3 mb-4">
        @foreach ([
            'Total transaksi' => number_format((int) $totals->transactions, 0, ',', '.'),
            'Voucher terjual' => number_format((int) $totals->units, 0, ',', '.'),
            'Pendapatan' => VoucherCatalog::formatRupiah((int) $totals->revenue),
        ] as $label => $value)
            <div class="col-md-4">
                <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                    <p class="text-secondary mb-1">{{ $label }}</p>
                    <p class="h3 fw-bold mb-0">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <section class="card rounded-4 border-0 shadow-sm p-3 p-md-4 mb-4">
        <h2 class="h5 fw-bold">Grafik transaksi lunas</h2>
        <p class="text-secondary small mb-3">14 hari terakhir · WIB</p>
        <div class="overflow-auto">
            <ol class="list-unstyled d-grid gap-2 mb-0" aria-label="Jumlah transaksi per tanggal" style="grid-template-columns: repeat(14, minmax(44px, 1fr)); min-width: 720px">
                @foreach ($days as $day)
                    <li class="text-center small">
                        <div class="d-flex flex-column justify-content-end align-items-center border-bottom pb-1" style="height: 148px">
                            <strong>{{ $day['count'] }}</strong>
                            <div class="rounded-top" aria-hidden="true" style="background: var(--vl-primary-light); width: 65%; height: {{ round(114 * $day['count'] / $chartMax) }}px"></div>
                        </div>
                        <div class="mt-2 text-secondary">{{ $day['label'] }}</div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="card rounded-4 border-0 shadow-sm p-3 p-md-4 mb-4">
        <h2 class="h5 fw-bold">Penjualan per paket</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th scope="col">Paket</th><th scope="col">Transaksi</th><th scope="col">Voucher terjual</th><th scope="col">Pendapatan</th></tr></thead>
                <tbody>
                    @forelse ($packages as $package)
                        <tr><td>{{ $package->voucher_name }}</td><td>{{ $package->transactions }}</td><td>{{ $package->units }}</td><td>{{ VoucherCatalog::formatRupiah((int) $package->revenue) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">Belum ada penjualan yang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card rounded-4 border-0 shadow-sm p-3 p-md-4 mb-4">
        <h2 class="h5 fw-bold">Riwayat pembelian</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th scope="col">Tanggal lunas</th><th scope="col">Referensi</th><th scope="col">Paket</th><th scope="col">Jumlah</th><th scope="col">Total dibayar</th></tr></thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr><td class="text-nowrap">{{ \Carbon\CarbonImmutable::parse($sale->paid_on)->locale('id')->translatedFormat('d M Y') }}</td><td>{{ $sale->reference }}</td><td>{{ $sale->voucher_name }}</td><td>{{ $sale->quantity }}</td><td>{{ VoucherCatalog::formatRupiah($sale->total) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-secondary">Belum ada pembelian lunas yang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (! ($preview ?? false) && $sales->hasPages())
            <div class="mt-3">{{ $sales->links('pagination::simple-bootstrap-5') }}</div>
        @endif
    </section>

    @unless ($preview ?? false)
    <details class="card rounded-4 border-0 shadow-sm p-3 p-md-4" @if ($errors->any()) open @endif>
        <summary class="h5 fw-bold mb-0">Catat pembelian lunas</summary>
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
            <div class="col-12"><button class="btn btn-brand" type="submit">Simpan pembelian</button></div>
        </form>
    </details>
    @endunless
</main>
@endsection
