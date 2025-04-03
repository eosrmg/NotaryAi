<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {return Inertia::render('Dashboard');})->name('dashboard');



Route::get('/calculator', [SalesController::class, 'index'])->name('calculator');
Route::post('/calculator', [SalesController::class, 'calculate'])->name('calculator.calculate');



Route::get('/exchange-rate', function () {
    try {
        $response = Http::get('https://codecrafthub.com/curs/bnr.php');

        if ($response->successful()) {
            $data = $response->json();

            // Return the entire array of rates, not just the first one
            return response()->json($data); 
        }

        return response()->json(['error' => 'Failed to fetch exchange rates'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

