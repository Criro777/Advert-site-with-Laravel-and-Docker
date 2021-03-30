<?php


namespace App\Services\Auth;


use App\Entity\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Sms\SmsSender;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginService
{
    use ThrottlesLogins;

    /**
     * @var string $username
     */
    protected $username = 'email';

    /**
     * @var \App\Services\Sms\SmsSender
     */
    protected $sms;

    /**
     * LoginService constructor.
     *
     * @param \App\Services\Sms\SmsSender $sms
     */
    public function __construct(SmsSender $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @param LoginRequest $request
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $authenticate = Auth::attempt(
            $request->only(['email', 'password']),
            $request->filled('remember')
        );

        if($authenticate) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            $user = Auth::user();
            if ($user->isWait()) {
                Auth::logout();
                return back()->with('error', 'Account is not confirmed. Please check your e-mail');
            }
            if ($user->isPhoneAuthEnabled()) {
                Auth::logout();
                $token = (string)random_int(10000, 99999);
                $request->session()->put('auth', [
                    'id' => $user->id,
                    'token' => $token,
                    'remember' => $request->filled('remember'),
                ]);
                $this->sms->send($user->phone, 'Login code: ' . $token);
                return redirect()->route('login.phone');
            }

            return redirect()->intended(route('cabinet.home'));
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            $this->username => [trans('auth.failed')],
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verify(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if (!$session = $request->session()->get('auth')) {
            throw new BadRequestHttpException('Missing token info.');
        }

        /** @var User $user */
        $user = User::findOrFail($session['id']);

        if ($request['token'] === $session['token']) {
            $request->session()->flush();
            $this->clearLoginAttempts($request);
            Auth::login($user, $session['remember']);
            return redirect()->intended(route('cabinet.home'));
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages(['token' => ['Invalid auth token.']]);
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }
}
