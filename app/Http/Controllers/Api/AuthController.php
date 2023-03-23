<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SenderRequest;
use App\Packages\Sms\Smsc\Smsc;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    private string $smsCodeRequestTemplate = 'Ваш код подтверждения для сайта profile.fluid-line.ru: %d';

    /**
     * Вход в личный кабинет
     *
     * @return JsonResponse
     */
    public function login(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userService = new UserService();

        $authRequest['phone'] = preg_replace('#\D+#', '', $authRequest['phone']);

        $email_hash = $userService->encryptUserData($authRequest['email']);
        $phone_hash = $userService->encryptUserData($authRequest['phone']);
        $password_hash = $userService->encryptUserData($authRequest['password']);

        $profile = Profile::where([
            'email' => $email_hash, 
            'phone' => $phone_hash, 
            'password' => $password_hash
        ])->first();

        if (!is_null($profile)) {

            $login = config('services.smsc.login');
            $password = config('services.smsc.password');

            $smsc = new Smsc($login, $password);
            $code = rand(1000, 9999);
           
            $profile->auth_sms_code = $code;
            $profile->save();

            // $smsc->send()->sendSmsMessage(
            //     sprintf($this->smsCodeRequestTemplate, $code), 
            //     [$authRequest['phone']]
            // );

            return new JsonResponse(['code' => $code], 201);
        } else {
            return new JsonResponse(['message' => 'Неверные данные для входа'], 403);
        }
    }

    /**
     * Отправка пользователем кода из СМС
     *
     * @return JsonResponse
     */
    public function smsSend(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userService = new UserService();

        $authRequest['phone'] = preg_replace('#\D+#', '', $authRequest['phone']);

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
