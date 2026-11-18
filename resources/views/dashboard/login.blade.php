@extends('layouts.landing')
@section('title', 'Masuk Dashboard | Connect Pro')
@section('content')
<main class="container py-5" style="max-width: 460px">
    <a class="d-inline-block mb-4" href="{{ route('voucher.landing') }}">← Katalog voucher</a>
    <section class="card border-0 shadow-sm rounded-4 p-4">
        <p class="text-primary fw-bold mb-2">Connect Pro</p>
        <h1 class="h3 fw-bold">Masuk dashboard</h1>
        <p class="text-secondary">Gunakan akun yang sudah diberi akses penjualan.</p>
        <form method="post" action="{{ route('login.store') }}">
            @csrf
            @foreach ($errors->all() as $error)
                <p class="alert alert-danger py-2" role="alert">{{ $error }}</p>
            @endforeach
            <label class="form-label" for="email">Email</label>
            <input class="form-control mb-3" id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" required>
            <label class="form-label" for="password">Kata sandi</label>
            <input class="form-control mb-4" id="password" name="password" type="password" autocomplete="current-password" required>
            <button class="btn btn-brand w-100" type="submit">Masuk</button>
        </form>
    </section>
</main>
@endsection
