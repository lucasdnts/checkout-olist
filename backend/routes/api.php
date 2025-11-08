<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\CouponController;

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\GatewayMockController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

#Checkout
Route::get('/plans', [PlanController::class, 'index']);
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
Route::post('/checkout', [CheckoutController::class, 'processarCheckout']);

#Gateway
Route::post('/gateway/process', [GatewayMockController::class, 'processPayment']);

#Subscriptions
Route::get('/subscriptions/{id}', [CheckoutController::class, 'retornarAssinatura']);

