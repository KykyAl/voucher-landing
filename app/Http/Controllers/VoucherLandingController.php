<?php

namespace App\Http\Controllers;

use App\Services\VoucherCatalog;
use Illuminate\View\View;

class VoucherLandingController extends Controller
{
    public function __invoke(VoucherCatalog $catalog): View
    {
        return view('voucher.index', [
            'brand' => $catalog->brand(),
            'vouchers' => $catalog->vouchers(),
            'steps' => $catalog->steps(),
            'maxQuantity' => $catalog->maxQuantity(),
        ]);
    }
}
