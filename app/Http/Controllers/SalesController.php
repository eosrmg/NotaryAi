<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        return Inertia::render('Calculator');
    }


    public function calculate(Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric',
        'is_eur' => 'required|boolean',
        'current_rate' => 'required|numeric',
        'is_pf' => 'required|boolean',
        'is_one_percent' => 'required|boolean',
        'apply_impozit' => 'required|boolean',
    ]);

    $amount = (float) $validated['amount'];
    $currentRate = (float) $validated['current_rate'];
    $isPF = (bool) $validated['is_pf'];
    $isOnePercent = (bool) $validated['is_one_percent'];
    $applyImpozit = (bool) $validated['apply_impozit'];

    // Calculate final amount (assuming it's already converted to RON if necessary)
    $finalSum = $amount;

    // Calculate Onorariu
    $onorariu = 0;

    if ($finalSum <= 20000) {
        $onorariu = max(230, $finalSum * 0.022);
    } elseif ($finalSum <= 35000) {
        $onorariu = 440 + ($finalSum - 20000) * 0.019;
    } elseif ($finalSum <= 65000) {
        $onorariu = 725 + ($finalSum - 35000) * 0.016;
    } elseif ($finalSum <= 100000) {
        $onorariu = 1205 + ($finalSum - 65000) * 0.015;
    } elseif ($finalSum <= 200000) {
        $onorariu = 1705 + ($finalSum - 100000) * 0.011;
    } elseif ($finalSum <= 600000) {
        $onorariu = 2805 + ($finalSum - 200000) * 0.009;
    } else {
        $onorariu = 6405 + ($finalSum - 600000) * 0.006;
    }

    // Calculate Onorariu Arhivare (Fixed amount)
    $onorariuArhivare = 45;

    // Calculate TVA (19% of the Onorariu + Onorariu Arhivare)
    $tva = 0.19 * ($onorariu + $onorariuArhivare);

    // Calculate Taxa CF
    $taxRate = $isPF ? 0.0015 : 0.005;
    $taxaCF = max($finalSum * $taxRate, 60);

    // Calculate Impozit
    $impozit = 0;
    if ($applyImpozit) {
        $impozit = $isOnePercent ? $finalSum * 0.01 : $finalSum * 0.03;
    }

    // Calculate Subtotal (Onorariu + Onorariu Arhivare + TVA)
    $subtotal = $onorariu + $onorariuArhivare + $tva;

    // Calculate Total (Includes Taxa CF and Impozit as well)
    $total = $subtotal + $taxaCF;

    return response()->json([
        'onorariu' => number_format($onorariu, 2),
        'onorariuArhivare' => number_format($onorariuArhivare, 2),
        'tva' => number_format($tva, 2),
        'subtotal' => number_format($subtotal, 2),
        'taxaCF' => number_format($taxaCF, 2),
        'impozit' => number_format($impozit, 2),
        'total' => number_format($total, 2),
    ]);
}

    

}
