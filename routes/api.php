<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingInformationController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf-cookie');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('products', ProductController::class)->only('index', 'show');
Route::apiResource('categories', CategoryController::class)->only('index', 'show');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('orders', OrderController::class)->only('index', 'show');
    Route::apiResource('payment-methods', PaymentMethodController::class)->only('index', 'show');

    Route::middleware(['check.role:1'])->group(function () {
        Route::apiResource('categories', CategoryController::class)->only('store', 'update', 'destroy');
        Route::apiResource('products', ProductController::class)->only('store', 'update', 'destroy');
        Route::apiResource('orders', OrderController::class)->only('update', 'destroy');
        Route::apiResource('payment-methods', PaymentMethodController::class)->only('store', 'update', 'destroy');
    });

    Route::middleware(['check.role:2'])->group(function () {
        Route::apiResource('carts', CartController::class);
        Route::apiResource('shipping-informations', ShippingInformationController::class);
        Route::apiResource('orders', OrderController::class)->only('store');
        Route::get('payment/{id}', [OrderController::class, 'paymentDetail']);
    });
});
