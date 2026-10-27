<?php

namespace App\Services;

/**
 * Sumber data landing voucher.
 *
 * Saat ini membaca data dummy dari config/voucher.php. Untuk beralih ke
 * database, cukup ganti isi method di kelas ini dengan query Eloquent
 * dan pertahankan bentuk array yang dikembalikan.
 */
class VoucherCatalog
{
    /**
     * @return array{name: string, tagline: string, description: string, support_contact: string}
     */
    public function brand(): array
    {
        return config('voucher.brand');
    }

    /**
     * @return list<array{code: string, name: string, description: string, face_value: int, price: int, validity_days: int, is_available: bool, is_featured: bool, discount_percent: int}>
     */
    public function vouchers(): array
    {
        return array_map(function (array $voucher): array {
            $voucher['discount_percent'] = $voucher['face_value'] > 0
                ? (int) round(($voucher['face_value'] - $voucher['price']) / $voucher['face_value'] * 100)
                : 0;

            return $voucher;
        }, config('voucher.vouchers', []));
    }

    /**
     * @return list<array{title: string, description: string}>
     */
    public function steps(): array
    {
        return config('voucher.steps', []);
    }

    public function maxQuantity(): int
    {
        return (int) config('voucher.max_quantity', 10);
    }

    public static function formatRupiah(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}
