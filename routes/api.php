<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('api')->group(function () {
    // Residents
    Route::apiResource('residents', ResidentController::class);

    // Houses
    Route::get('houses/{house}/history', [HouseController::class, 'history']);
    Route::get('houses/{house}/payment-history', [HouseController::class, 'paymentHistory']);
    Route::post('houses/{house}/add-resident', [HouseController::class, 'addResident']);
    Route::post('houses/{house}/remove-resident', [HouseController::class, 'removeResident']);
    Route::apiResource('houses', HouseController::class);

    // Payments
    Route::get('payments/summary', [PaymentController::class, 'summary']);
    Route::get('payments/monthly-detail', [PaymentController::class, 'monthlyDetail']);
    Route::apiResource('payments', PaymentController::class);

    // Expenses
    Route::apiResource('expenses', ExpenseController::class);

    // Statistics
    Route::get('statistics/dashboard', [StatisticsController::class, 'dashboard']);
});
