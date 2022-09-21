<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataCollectorController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPasswordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaystatusController;
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

Route::prefix('collect')->group(function () {
    Route::post('/invoice', [InvoiceController::class, 'getInvoice']);
    Route::post('/pay-status-update', [PaystatusController::class, 'updateStatus']);
});

Route::post('/forgot-password', [UserPasswordController::class, 'forgottenPassword']);

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::get('/info', 'getUserInfo');
    Route::post('/password-reset', 'resetPassword');
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::get('/logout', 'logout');

    Route::prefix('sms')->group(function () {
        Route::post('/resend', 'smsResend');
        Route::post('/send', 'smsSend');
    });
});

Route::controller(OrdersController::class)->prefix('order')->group(function () {
    Route::get('/list/{page}', 'getOrderList');
    Route::get('/{orderId}', 'getOrderId');
});

Route::controller(ManagerController::class)->prefix('manager')->group(function () {
    Route::get('/{managerId}/info', 'getManagerInfo');
    Route::post('/{managerId}/send-message', 'sendEmailMessage');
});

Route::controller(DownloadController::class)->prefix('download')->group(function () {
    Route::post('/invoice-documents/{docId}', 'downloadFileById');
    Route::post('/invoice-documents/{docType}/all', 'downloadFiles');
    Route::post('/invoice-documents/all', 'downloadGeneralArchive');
});
