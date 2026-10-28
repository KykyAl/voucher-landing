<?php

namespace Tests\Feature;

use App\Services\VoucherCatalog;
use Tests\TestCase;

class VoucherLandingTest extends TestCase
{
    public function test_landing_page_displays_brand_vouchers_and_steps(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('lang="id"', false)
            ->assertSee('SO NET')
            ->assertSee('Voucher Belanja 100 Ribu')
            ->assertSee('Rp92.500')
            ->assertSee('Berlaku 60 hari setelah pembelian')
            ->assertSee('Cara Pembelian')
            ->assertSee('Pembayaran belum tersedia.');
    }

    public function test_cart_drawer_uses_configured_maximum_quantity(): void
    {
        config(['voucher.max_quantity' => 5]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('id="cart"', false)
            ->assertSee('data-max-quantity="5"', false)
            ->assertSee('maksimal 5 voucher per pembelian')
            ->assertSee('data-cart-add value="VCR-100"', false);
    }

    public function test_unavailable_voucher_cannot_be_selected(): void
    {
        $response = $this->get('/');

        $response->assertSee('Stok habis');

        $this->assertMatchesRegularExpression(
            '/value="VCR-500"[^>]*disabled/s',
            $response->getContent()
        );
    }

    public function test_voucher_data_can_be_replaced_without_touching_the_view(): void
    {
        config(['voucher.vouchers' => [[
            'code' => 'TEST-01',
            'name' => 'Voucher Uji',
            'description' => 'Data pengganti.',
            'face_value' => 20000,
            'price' => 15000,
            'validity_days' => 7,
            'is_available' => true,
            'is_featured' => false,
        ]]]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Voucher Uji')
            ->assertSee('Rp15.000')
            ->assertSee('Hemat 25%')
            ->assertDontSee('Voucher Belanja 100 Ribu');
    }

    public function test_format_rupiah_uses_indonesian_thousand_separator(): void
    {
        $this->assertSame('Rp1.250.000', VoucherCatalog::formatRupiah(1250000));
    }
}
