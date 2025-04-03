<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Sales extends Model
{
    protected $fillable = [
        'amount', 'is_eur', 'current_rate', 'selected_date', 
        'is_pf', 'is_one_percent', 'apply_impozit'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_eur' => 'boolean',
        'current_rate' => 'float',
        'selected_date' => 'date',
        'is_pf' => 'boolean',
        'is_one_percent' => 'boolean',
        'apply_impozit' => 'boolean'
    ];

    public function calculateOnorariu($amount)
    {
        if ($amount <= 20000) {
            return max(230, $amount * 0.022);
        } elseif ($amount <= 35000) {
            return 440 + ($amount - 20000) * 0.019;
        } elseif ($amount <= 65000) {
            return 725 + ($amount - 35000) * 0.016;
        } elseif ($amount <= 100000) {
            return 1205 + ($amount - 65000) * 0.015;
        } elseif ($amount <= 200000) {
            return 1705 + ($amount - 100000) * 0.011;
        } elseif ($amount <= 600000) {
            return 2805 + ($amount - 200000) * 0.009;
        } else {
            return 6405 + ($amount - 600000) * 0.006;
        }
    }
}
