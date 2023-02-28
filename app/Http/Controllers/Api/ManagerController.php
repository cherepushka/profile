<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerRequest;
use App\Mail\ManagerMessage;
use App\Mail\UserCreated;
use App\Models\Manager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ManagerController extends Controller
{
    /**
     * Получение информации о менеджере
     *
     * @param $managerId
     * @return JsonResponse
     */
    public function managerInfo($managerId): JsonResponse
    {
        $manager = Manager::where(['id' => $managerId])->first();

        if (!is_null($manager)) {
            return new JsonResponse([
                'name' => $manager->name,
                'image' => $manager->image,
                'email' => $manager->email,
                'phone' => $manager->phone,
                'whats_app' => $manager->whats_app,
                'position' => $manager->position,
            ]);

        } else {
            return new JsonResponse(['error' => 'Manager is undefined']);
        }

        return new JsonResponse(['error' => 'Crash happend :(']);
    }

    /**
     * Отправка сообщения менеджеру на имейл
     *
     * @param $managerId
     * @return JsonResponse
     */
    public function managerSendMessage(ManagerRequest $request, int $managerId): JsonResponse
    {
        $managerRequest = $request->validated();

        $manager = Manager::where(['id' => $managerId])->first();
        if (is_null($manager)) {
            return new JsonResponse(['error' => 'Manager is undefined'], 404);
        }

        if (env('APP_DEBUG')) {
            $manager->email = "fluidmi@rambler.ru";
        }

        Mail::to($manager->email)->send(new ManagerMessage([
            'user_email' => $managerRequest['email'],
            'user_phone' => $managerRequest['phone'],
            'user_message' => $managerRequest['message']
        ]));

        return new JsonResponse(['message' => 'Письмо успешно отправлено'], 200);
    }
}
