<?php


namespace App\Services\Auth;


use App\Entity\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    use ThrottlesLogins;

    /**
     * @var string $username
     */
    protected $username = 'email';

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
            if ($user->status != USER::STATUS_ACTIVE) {
                Auth::logout();
                return back()->with('error', 'Account is not confirmed. Please check your e-mail');
            }
            return redirect()->intended(route('cabinet'));
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            $this->username => [trans('auth.failed')],
        ]);
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }
}
