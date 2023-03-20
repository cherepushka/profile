<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserInfo;
use App\Models\ProfileInternal;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    /**
     * Получение информации о залогиненном в данный момент пользователе
     */
    public function userInfo(): UserInfo
    {
        return new UserInfo(auth()->user());
    }

    /**
     * Выход из личного кабинета
     *
     * @return JsonResponse
     */
    public function userLogout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return new JsonResponse([
            'status' => true,
            'message' => 'Logout successfully',
        ]);

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
