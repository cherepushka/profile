<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class UserPasswordController extends Controller
{
    /**
     * Запрос на восстановление пароля
     *
     * @return JsonResponse
     */
    public function forgottenPassword(): JsonResponse
    {
        return response()->json(['message' => config('constants.REQUEST_SUCCESS_RU')]);
    }
}
