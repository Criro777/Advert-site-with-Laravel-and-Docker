<?php


namespace App\Services\Auth;


use App\Entity\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\Auth\VerifyMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Mail\Mailer as MailerInterface;
use Illuminate\Contracts\Events\Dispatcher;

class RegisterService
{
    /**
     * @var MailerInterface $mailer
     */
    protected $mailer;

    /**
     * @var Dispatcher $dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new service instance.
     *
     * @param MailerInterface $mailer
     * @param Dispatcher $dispatcher
     * @return void
     */
    public function __construct(MailerInterface $mailer, Dispatcher $dispatcher)
    {
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create a new user instance after registration and send email to verify.
     *
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request): void
    {
        $user = User::register(
            $request['name'],
            $request['email'],
            $request['password']
        );

        $this->mailer->to($user->email)->send(new VerifyMail($user));
        $this->dispatcher->dispatch(new Registered($user));
    }

    /**
     * @param $id
     */
    public function verify($id): void
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->verify();
    }
}
