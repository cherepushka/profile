<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPasswordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PayStatusController;

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
Route::post('/auth/forgot-password', [UserPasswordController::class, 'forgottenPassword']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/login', 'login');

    Route::post('auth/sms/send', 'smsSend');
    Route::post('auth/sms/resend', 'smsResend');
});

Route::prefix('collect')->group(function () {
    Route::post('/invoice', [InvoiceController::class, 'getInvoice']);
    Route::post('/pay-status-update', [PayStatusController::class, 'updateStatus']);
});

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::controller(DownloadController::class)->prefix('download')->group(function () {
        Route::get('/invoice-documents/{docId}', 'downloadFileById');
        Route::get('/invoice-documents/all/{orderId}', 'downloadOrderArchive');
    });

    Route::controller(ManagerController::class)->group(function () {
        Route::get('/manager/{managerId}/info', 'managerInfo');
        Route::post('/manager/{managerId}/send-message', 'managerSendMessage');
    });

    Route::controller(OrdersController::class)->prefix('order')->group(function () {
        Route::get('/list/{page}', 'orderList');
        Route::get('/{orderId}', 'orderInfo');
        Route::post('/{orderId}/edit/custom-value', 'editCustomValue');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get("/user/info", [UserController::class, 'userInfo']);
        Route::post("/user/logout", [UserController::class, 'userLogout']);
    });

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/info', 'userInfo');
        Route::get('/logout', 'userLogout');
        Route::post('/password-reset', 'resetPassword');
    });
});



