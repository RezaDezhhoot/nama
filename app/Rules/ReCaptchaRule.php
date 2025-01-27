<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use ReCaptcha\ReCaptcha;

class ReCaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $recaptcha = new ReCaptcha(config('services.recaptcha.secret_key'));
        $resp = $recaptcha->verify($value, request()->ip());

        if (! $resp->isSuccess()) {
            $fail('فیلد امنیتی ناموفق');
        }
    }
}
