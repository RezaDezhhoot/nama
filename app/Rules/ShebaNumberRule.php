<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ShebaNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = str_replace(" ","",trim(mb_strtoupper($value)));

        if (! str_starts_with($value,'IR'))
            $fail('شماره شبا نامعتبر می باشد');

        if (strlen($value) != 26)
            $fail('شماره شبا نامعتبر می باشد');

        $value = substr($value,4)."1827".substr($value,2,2);
        if (my_bcmod($value , 97) != 1)
            $fail('شماره شبا نامعتبر می باشد');
    }
}
