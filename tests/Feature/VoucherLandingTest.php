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
            ->assertSee('Connect Pro')
            ->assertSee('Connect Pro × Pasar Jaya — Beli Voucher')
            ->assertSee('Voucher WiFi 1 Jam')
            ->assertSee('Rp5.000')
            ->assertSee('Rp10.000')
            ->assertSee('Rp25.000')
            ->assertSee('1 Minggu')
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
            ->assertSee('data-cart-add value="CP-1HARI"', false);
    }

    public function test_unavailable_voucher_cannot_be_selected(): void
    {
        config(['voucher.vouchers' => [[
            'code' => 'CP-HABIS',
            'name' => 'Voucher Habis',
            'description' => 'Stok kosong.',
            'face_value' => 5000,
            'price' => 5000,
            'duration' => '1 Jam',
            'is_available' => false,
            'is_featured' => false,
        ]]]);

        $response = $this->get('/');

        $response->assertSee('Stok habis');

        $this->assertMatchesRegularExpression(
            '/value="CP-HABIS"[^>]*disabled/s',
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
            'duration' => '3 Hari',
            'is_available' => true,
            'is_featured' => false,
        ]]]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Voucher Uji')
            ->assertSee('Rp15.000')
            ->assertSee('Hemat 25%')
            ->assertSee('3 Hari')
            ->assertDontSee('Voucher WiFi 1 Jam');
    }

    public function test_assets_are_versioned_by_content_to_bust_browser_cache(): void
    {
        $cssHash = hash_file('crc32b', public_path('css/voucher-landing.css'));
        $jsHash = hash_file('crc32b', public_path('js/voucher-landing.js'));

        $this->get('/')
            ->assertSee('css/voucher-landing.css?v='.$cssHash, false)
            ->assertSee('js/voucher-landing.js?v='.$jsHash, false);
    }

    public function test_format_rupiah_uses_indonesian_thousand_separator(): void
    {
        $this->assertSame('Rp1.250.000', VoucherCatalog::formatRupiah(1250000));
    }
}
