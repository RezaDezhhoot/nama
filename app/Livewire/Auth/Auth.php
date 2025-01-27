<?php

namespace App\Livewire\Auth;

use App\Livewire\BaseComponent;
use App\Rules\ReCaptchaRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Illuminate\Support\Facades\Auth as AuthFacades;

class Auth extends BaseComponent
{
    public $recaptcha;
    public $email , $password;


    public function mount()
    {
        AuthFacades::loginUsingId(24);
    }

    public function render()
    {
        return view('livewire.auth.auth')->extends('livewire.layouts.auth');
    }

    public function loadRecaptcha(): void
    {
        $this->dispatch('loadRecaptcha');
    }

    public function login()
    {
        $rate_limiter_key1 = 'admin-login:'.request()->ip();
        if (
            RateLimiter::tooManyAttempts($rate_limiter_key1, 5)
        ) {
            return $this->addError('password',__('general.too_many_request'));
        }
        RateLimiter::hit($rate_limiter_key1 ,  3 * 60 * 60);

        $this->validate([
            'email' => ['required','string','max:250'],
            'password' => ['required','string','max:250'],
            'recaptcha' => ['required',new ReCaptchaRule]
        ]);

        $rate_limiter_key2 = 'admin-login:'.$this->email;
        if (RateLimiter::tooManyAttempts($rate_limiter_key2, 5)) {
            return $this->addError('password',__('general.too_many_request'));
        }
        RateLimiter::hit($rate_limiter_key2 ,  3 * 60 * 60);

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
            fn(Builder $query) => $query->panelAccess(),
        ];

        $credentials2 = [
            'phone' => $this->email,
            'password' => $this->password,
            fn(Builder $query) => $query->panelAccess(),
        ];

        if (AuthFacades::attempt($credentials,true) || AuthFacades::attempt($credentials2,true)) {
            RateLimiter::clear($rate_limiter_key1);
            RateLimiter::clear($rate_limiter_key2);
            request()->session()->regenerate();
            return redirect()->intended(route('admin.dashboard.index'));
        } else
            return $this->addError('password',__('validation.current_password'));
    }

    private function resetInputs(): void
    {
        $this->reset(['email', 'password']);
    }
}
