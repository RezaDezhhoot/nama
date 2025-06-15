<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JDateRule implements ValidationRule
{
    public function __construct(public $sep = "-")
    {

    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = explode($this->sep,$value);
        if (sizeof($data) < 3) {
            $fail("تاریخ نامعتبر");
            return;
        }
        if (ceil(log10($data[0])) != 4 || $data[0] < 1250 || $data[0] > 1450) {
            $fail("تاریخ نامعتبر");
            return;
        }
        if ($data[1] < 1 || $data[1] > 12) {
            $fail("تاریخ نامعتبر");
            return;
        }
        if ($data[2] < 1 || $data[2] > 31) {
            $fail("تاریخ نامعتبر");
            return;
        }
    }
}
