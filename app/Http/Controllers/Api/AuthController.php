<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\ProfileAuth;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SenderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

class AuthController extends Controller
{
    /**
     * Вход в личный кабинет
     *
     * @return JsonResponse
     */
    public function login(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userService = new UserService();

        $email_hash = $userService->encryptUserData($authRequest['email']);
        $phone_hash = $userService->encryptUserData($authRequest['phone']);
        $password_hash = $userService->encryptUserData($authRequest['password']);

        $profile = Profile::where([
            'email' => $email_hash, 
            'phone' => $phone_hash, 
            'password' => $password_hash
        ])->first();

        if (!is_null($profile)) {

            $profile->auth_sms_code = 1234;
            $profile->save();

            return new JsonResponse([], 201);
        } else {
            return new JsonResponse(['message' => 'Неверные данные для входа'], 403);
        }
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
    public function smsSend(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userService = new UserService();

        $email_hash = $userService->encryptUserData($authRequest['email']);
        $phone_hash = $userService->encryptUserData($authRequest['phone']);
        $password_hash = $userService->encryptUserData($authRequest['password']);
        $sms_code = (int)$authRequest['sms_code'];

        $profile = Profile::where([
            'email' => $email_hash, 
            'phone' => $phone_hash, 
            'password' => $password_hash
        ])->first();

        if ($sms_code === $profile->auth_sms_code) {

            $token_name = md5('auth:' . $email_hash . $phone_hash);

            $profile->tokens()->where('name', $token_name)->delete();

            $date = new \DateTime(date('Y-m-d H:i:s', strtotime('+ 7 days')));
            $token = $profile->createToken($token_name, ['server:select'], $date);

            $profile->remember_token = $token->plainTextToken;
            $profile->save();

            return new JsonResponse(['message' => ['token' => $token->plainTextToken]]);
        } else {
            return new JsonResponse(['message' => 'Неверный код'], 403);
        }
    }
}
