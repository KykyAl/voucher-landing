<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['reference', 'voucher_code', 'voucher_name', 'quantity', 'total', 'paid_on'])]
class VoucherSale extends Model
{
    protected function casts(): array
    {
        return ['quantity' => 'integer', 'total' => 'integer'];
    }
}

