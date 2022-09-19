<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Получение информации о менеджере
     *
     * @param $managerId
     * @return JsonResponse
     */
    public function getManagerInfo($managerId): JsonResponse
    {
        return response()->json([
            "name" => "string",
            "photo" => "string",
            "email" => "kam@fluid-line.ru",
            "phone" => "+7(495) 984-41-01 (доб.123)",
            "whats_app" => "+7(495) 984-41-01",
            "position" => "помощник менеджера"
        ]);
    }

    /**
     * Отправка сообщения менеджеру на имейл
     *
     * request json_body [ "email" => "string", "phone" => "string", "message" => "string" ]
     *
     * @param $managerId
     * @return JsonResponse
     */
    public function sendEmailMessage($managerId): JsonResponse
    {
        return response()->json(['message' => config('constants.REQUEST_SUCCESS_RU')]);
    }
}
