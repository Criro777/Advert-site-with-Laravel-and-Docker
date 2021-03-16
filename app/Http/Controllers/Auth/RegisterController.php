<?php

namespace App\Http\Controllers\Auth;

use App\Entity\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Services\Auth\RegisterService;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    /**
     * @var RegisterService $service
     */
    protected $service;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/cabinet';

    /**
     * Create a new controller instance.
     *
     * @param RegisterService $service
     */
    public function __construct(RegisterService $service)
    {
        $this->middleware('guest');
        $this->service = $service;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    protected function register(RegisterRequest $request): RedirectResponse
    {
        $this->service->register($request);

        return redirect()->route('login')
            ->with('success', 'Check your email and follow the link to verify your registration');
    }

    /**
     * Verify a user after following the link from email
     *
     * @param string $token
     * @return RedirectResponse
     */
    public function verify(string $token): RedirectResponse
    {
        if (!$user = User::where('verify_token', $token)->first()) {
            return redirect()->route('login')
                ->with('error', 'Incorrect data to login. Please try again.');
        }

        try {
            $this->service->verify($user->id);
            return redirect()->route('login')
                ->with('success', 'Your e-mail has been verified. Please enter with your credentials.');
        } catch (\DomainException $e) {
            return redirect()->route('login')
                ->with('error', $e->getMessage());
        }
    }
}
