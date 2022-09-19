<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Получение информации о залогиненном в данный момент пользователе
     *
     * @return JsonResponse
     */
    public function getUserInfo(): JsonResponse
    {
        return response()->json(['id' => 10, 'registration_date' => 1663054337]);
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
