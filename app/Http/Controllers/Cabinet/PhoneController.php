<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\PhoneVerifyRequest;
use App\Services\Profile\PhoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{
    /**
     * @var \App\Services\Profile\PhoneService
     */
    private $service;

    /**
     * PhoneController constructor.
     *
     * @param \App\Services\Profile\PhoneService $service
     */
    public function __construct(PhoneService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function send(): RedirectResponse
    {
        try {
            $this->service->send(Auth::id());
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.profile.phone');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();

        return view('cabinet.profile.phone', compact('user'));
    }

    /**
     * @param \App\Http\Requests\Cabinet\PhoneVerifyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function verify(PhoneVerifyRequest $request): RedirectResponse
    {
        try {
            $this->service->verify(Auth::id(), $request);
        } catch (\DomainException $e) {
            return redirect()->route('cabinet.profile.phone')->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.profile.home');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function auth(): RedirectResponse
    {
        $this->service->toggleAuth(Auth::id());

        return redirect()->route('cabinet.profile.home');
    }
}
