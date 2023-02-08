<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Вход в личный кабинет
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        return response()->json(['id' => 10, 'registration_date' => 1663054337]);
    }

    /**
     * Запросить повторную отправку СМС
     *
     * ToDo: Если контроллер в результате будет перегружен, создать SMSController
     *
     * request json_body [ 'email' => "string", 'password' => "string", 'phone' => "string" ]
     * @return JsonResponse
     */
    public function smsResend(): JsonResponse
    {
        return response()->json(['message' => config('constants.REQUEST_SUCCESS_RU')]);
    }

    /**
     * Отправка пользователем кода из СМС
     *
     * ToDo: Если контроллер в результате будет перегружен, создать SMSController
     *
     * request json_body [ 'code' => "string" ]
     * @return JsonResponse
     */
    public function smsSend(): JsonResponse
    {
        return response()->json(["password" => "string", "phone" => "string", "email" => "string", "code" => "string"]);
    }
}
