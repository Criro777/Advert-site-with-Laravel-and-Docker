<?php

namespace App\Services\Profile;

use App\Entity\User;
use App\Http\Requests\Cabinet\PhoneVerifyRequest;
use App\Services\Sms\SmsSender;
use Carbon\Carbon;

class PhoneService
{
    /**
     * @var \App\Services\Sms\SmsSender
     */
    private $sms;

    /**
     * PhoneService constructor.
     *
     * @param \App\Services\Sms\SmsSender $sms
     */
    public function __construct(SmsSender $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @param $id
     * @throws \Throwable
     */
    public function send($id)
    {
        $user = $this->getUser($id);

        $token = $user->requestPhoneVerification(Carbon::now());
        $this->sms->send($user->phone, 'Phone verification token: ' . $token);
    }

    /**
     * @param $id
     * @param \App\Http\Requests\Cabinet\PhoneVerifyRequest $request
     * @throws \Throwable
     */
    public function verify($id, PhoneVerifyRequest $request)
    {
        $user = $this->getUser($id);
        $user->verifyPhone($request['token'], Carbon::now());
    }

    /**
     * @param $id
     * @return bool
     * @throws \Throwable
     */
    public function toggleAuth($id): bool
    {
        $user = $this->getUser($id);
        if ($user->isPhoneAuthEnabled()) {
            $user->disablePhoneAuth();
        } else {
            $user->enablePhoneAuth();
        }
        return $user->isPhoneAuthEnabled();
    }

    /**
     * @param $id
     * @return \App\Entity\User
     */
    private function getUser($id): User
    {
        return User::findOrFail($id);
    }
}
