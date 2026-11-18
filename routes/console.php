<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('voucher:dashboard-access {email} {--revoke}', function () {
    $email = strtolower(trim($this->argument('email')));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Masukkan alamat email yang valid.');

        return 1;
    }

    $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$email])->first();
    if (! $user) {
        if ($this->option('revoke')) {
            $this->error('Akun tidak ditemukan.');

            return 1;
        }

        $name = trim((string) $this->ask('Nama akun'));
        $password = $this->secret('Kata sandi (minimal 12 karakter)');
        if ($name === '' || mb_strlen($name) > 255 || strlen((string) $password) < 12 || strlen($password) > 72 || trim($password) !== $password) {
            $this->error('Nama dan kata sandi tidak valid.');

            return 1;
        }

        $user = new \App\Models\User(['name' => $name, 'email' => $email, 'password' => $password]);
    }

    $user->can_view_sales = ! $this->option('revoke');
    $user->save();
    $this->info($user->can_view_sales ? 'Akses dashboard diberikan.' : 'Akses dashboard dicabut.');

    return 0;
})->purpose('Berikan atau cabut akses dashboard penjualan voucher');
