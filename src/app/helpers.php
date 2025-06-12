<?php

use Silaswint\LaravelNumberHelper\App\Number\Number;

if (! function_exists('number')) {
    function number(float|int|null $amount): ?Number
    {
        if ($amount === null) {
            return null;
        }

        return Number::make($amount);
    }
}
