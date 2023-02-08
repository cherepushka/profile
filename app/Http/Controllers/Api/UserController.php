<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Получение информации о залогиненном в данный момент пользователе
     *
     * @return JsonResponse
     */
    public function userInfo(): JsonResponse
    {
        return response()->json(['id' => 10, 'registration_date' => time()]);
    }

    /**
     * Выход из личного кабинета
     *
     * @return JsonResponse
     */
    public function userLogout(): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Запрос на восстановление пароля
     *
     * @return JsonResponse
     */
    public function resetPassword(): JsonResponse
    {
        return response()->json(['message' => config('constants.REQUEST_SUCCESS_RU')]);
    }
}
