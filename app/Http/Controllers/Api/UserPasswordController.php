<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
