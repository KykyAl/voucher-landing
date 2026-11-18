<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_a_granted_account_and_checks_access_on_every_request(): void
    {
        $user = User::factory()->create();

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('dashboard.sales.store'))->assertRedirect(route('login'));
        $credentials = ['email' => $user->email, 'password' => 'password'];
        $this->post(route('login.store'), $credentials)->assertSessionHasErrors('email');
        $this->assertGuest();

        $user->forceFill(['can_view_sales' => true])->save();
        $this->post(route('login.store'), $credentials)->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('Belum ada pembelian lunas yang tercatat.');

        $user->forceFill(['can_view_sales' => false])->save();
        auth()->forgetGuards();
        $this->get(route('dashboard'))->assertForbidden();
        $this->post(route('dashboard.sales.store'))->assertForbidden();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_confirmed_sales_update_the_totals_chart_and_tables_without_duplicates(): void
    {
        $user = User::factory()->create(['can_view_sales' => true]);
        $sale = [
            'reference' => 'BAYAR-001',
            'voucher_code' => 'CP-1HARI',
            'quantity' => 3,
            'total' => 30000,
            'paid_on' => CarbonImmutable::today('Asia/Jakarta')->toDateString(),
        ];

        $this->actingAs($user)->post(route('dashboard.sales.store'), $sale)->assertRedirect(route('dashboard'));
        $this->post(route('dashboard.sales.store'), $sale)->assertSessionHasErrors('reference');
        $this->assertDatabaseCount('voucher_sales', 1);

        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('totals', fn ($totals) => (int) $totals->transactions === 1 && (int) $totals->units === 3)
            ->assertViewHas('days', fn ($days) => $days->count() === 14 && $days->last()['count'] === 1)
            ->assertSee('Rp30.000')
            ->assertSee('BAYAR-001');
    }
}
