<?php

namespace App\Http\Controllers;

use App\Models\VoucherSale;
use App\Services\VoucherCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VoucherSalesController extends Controller
{
    public function index(VoucherCatalog $catalog): View
    {
        $today = CarbonImmutable::today('Asia/Jakarta');
        $start = $today->subDays(13);
        $sales = VoucherSale::query();

        $totals = (clone $sales)->selectRaw('COUNT(*) AS transactions, SUM(quantity) AS units, SUM(total) AS revenue')->first();
        $packages = (clone $sales)->selectRaw('voucher_code, MAX(voucher_name) AS voucher_name, COUNT(*) AS transactions, SUM(quantity) AS units, SUM(total) AS revenue')
            ->groupBy('voucher_code')->orderByDesc('units')->orderBy('voucher_code')->get();
        $byDay = (clone $sales)->whereBetween('paid_on', [$start->toDateString(), $today->toDateString()])
            ->selectRaw('paid_on, COUNT(*) AS transactions')->groupBy('paid_on')->get()
            ->mapWithKeys(fn (VoucherSale $sale) => [$sale->paid_on => (int) $sale->transactions]);
        $days = collect(range(0, 13))->map(function (int $offset) use ($start, $byDay): array {
            $date = $start->addDays($offset);

            return ['label' => $date->locale('id')->translatedFormat('d M'), 'count' => $byDay->get($date->toDateString(), 0)];
        });

        return view('dashboard.index', [
            'totals' => $totals,
            'packages' => $packages,
            'days' => $days,
            'chartMax' => max(1, $days->max('count')),
            'sales' => $sales->orderByDesc('paid_on')->orderByDesc('id')->paginate(15),
            'vouchers' => $catalog->vouchers(),
            'today' => $today->toDateString(),
        ]);
    }

    public function preview(VoucherCatalog $catalog): View
    {
        abort_unless(app()->environment('local'), 404);

        $today = CarbonImmutable::today('Asia/Jakarta');
        $vouchers = collect($catalog->vouchers())->where('is_available', true)->values();
        $sales = collect();
        $days = collect([1, 0, 2, 0, 1, 0, 2, 1, 0, 2, 0, 1, 2, 2])
            ->map(function (int $count, int $offset) use ($today, $vouchers, $sales): array {
                $date = $today->subDays(13 - $offset);

                for ($index = 0; $index < $count; $index++) {
                    $voucher = $vouchers[($offset + $index) % $vouchers->count()];
                    $quantity = ($offset + $index) % 4 + 1;
                    $sales->push((object) [
                        'reference' => sprintf('DEMO-%03d', $sales->count() + 1),
                        'voucher_name' => $voucher['name'],
                        'quantity' => $quantity,
                        'total' => $voucher['price'] * $quantity,
                        'paid_on' => $date->toDateString(),
                    ]);
                }

                return ['label' => $date->locale('id')->translatedFormat('d M'), 'count' => $count];
            });

        return view('dashboard.index', [
            'preview' => true,
            'totals' => (object) [
                'transactions' => $sales->count(),
                'units' => $sales->sum('quantity'),
                'revenue' => $sales->sum('total'),
            ],
            'packages' => $sales->groupBy('voucher_name')->map(fn ($rows, $name) => (object) [
                'voucher_name' => $name,
                'transactions' => $rows->count(),
                'units' => $rows->sum('quantity'),
                'revenue' => $rows->sum('total'),
            ])->sortByDesc('units'),
            'days' => $days,
            'chartMax' => max(1, $days->max('count')),
            'sales' => $sales->reverse(),
        ]);
    }

    public function store(Request $request, VoucherCatalog $catalog): RedirectResponse
    {
        $vouchers = collect($catalog->vouchers())->keyBy('code');
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:80', 'unique:voucher_sales'],
            'voucher_code' => ['required', Rule::in($vouchers->keys()->all())],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'total' => ['required', 'integer', 'min:1', 'max:10000000000'],
            'paid_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.CarbonImmutable::today('Asia/Jakarta')->toDateString()],
        ]);

        try {
            VoucherSale::create($data + ['voucher_name' => $vouchers[$data['voucher_code']]['name']]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['reference' => 'Referensi transaksi ini sudah tercatat.']);
        }

        return redirect()->route('dashboard')->with('status', 'Pembelian lunas telah dicatat.');
    }
}
