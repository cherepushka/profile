<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataCollectorController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPasswordController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/data-collector', [DataCollectorController::class, 'getRequest']);

Route::post('/forgot-password', [UserPasswordController::class, 'forgottenPassword']);

Route::controller(UserController::class)->group(function () {
    Route::get('/user/user-info', 'getUserInfo');
    Route::post('/user/password-reset', 'resetPassword');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/login', 'login');
    Route::get('/auth/logout', 'logout');
    Route::post('/auth/sms/resend', 'smsResend');
    Route::post('/auth/sms/send', 'smsSend');
});

Route::controller(OrdersController::class)->group(function () {
    Route::get('/order/list/{page}', 'getOrderList');
    Route::get('/order/{orderId}', 'getOrderId');
});

Route::controller(ManagerController::class)->group(function () {
    Route::get('/manager/{managerId}/info', 'getManagerInfo');
    Route::post('/manager/{managerId}/send-message', 'sendEmailMessage');
});

Route::controller(DownloadController::class)->group(function () {
    Route::post('/download/invoice-documents/{docId}', 'downloadFileById');
    Route::post('/download/invoice-documents/{docType}/all', 'downloadFiles');
    Route::post('/download/invoice-documents/all', 'downloadGeneralArchive');
});
