<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Packages\Crypto\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\SenderRequest;
use App\Packages\Sms\Smsc\Smsc;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private string $smsCodeRequestTemplate = 'Ваш код подтверждения для сайта profile.fluid-line.ru: %d';

    /**
     * Вход в личный кабинет
     *
     * @param SenderRequest $request
     * @return JsonResponse
     */
    public function login(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userCrypto = new User();

        // $authRequest['phone'] = preg_replace('#\D+#', '', $authRequest['phone']);

        $email_hash = $userCrypto->encryptUserData($authRequest['email']);
        $password_hash = $userCrypto->encryptUserData($authRequest['password']);

        $profile = Profile::where([
            'email' => $email_hash,
            'password' => $password_hash
        ])->first();

        if (!is_null($profile)) {

            // $login = config('services.smsc.login');
            // $password = config('services.smsc.password');
            // $smsc = new Smsc($login, $password);

            $code = 1234;

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
     * @param SenderRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function smsSend(SenderRequest $request): JsonResponse
    {
        $authRequest = $request->validated();
        $userCrypto = new User();

        $email_hash = $userCrypto->encryptUserData($authRequest['email']);
        $password_hash = $userCrypto->encryptUserData($authRequest['password']);
        $sms_code = (int)$authRequest['sms_code'];

        $profile = Profile::where([
            'email' => $email_hash,
            'password' => $password_hash
        ])->first();

        if ($sms_code === $profile->auth_sms_code) {

            $token_name = md5('auth:' . $email_hash);

            $profile->tokens()->where('name', $token_name)->delete();

            $date = new \DateTime(date('Y-m-d H:i:s', strtotime('+ 7 days')));
            $token = $profile->createToken($token_name, ['server:select'], $date);

            $profile->remember_token = $token->plainTextToken;
            $profile->save();

            return new JsonResponse([
                'id' => $profile->id,
                'token' => $token->plainTextToken,
                'registrationDate' => $profile->created_at->timestamp
            ]);
        } else {
            return new JsonResponse(['message' => 'Неверный код'], 403);
        }
    }
}
