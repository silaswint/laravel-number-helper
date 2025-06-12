<?php

namespace Silaswint\LaravelNumberHelper\App\Currency;

use Illuminate\Support\Number;

class Currency
{
    public function __construct(
        private float|int|null  $amount,
        private readonly string $locale = 'de',
    ) {
        $this->amount = $this->amount ?? 0;
    }

    public function euro(): string
    {
        return Number::currency($this->amount, in: 'EUR', locale: $this->locale);
    }

    public function dollar(): string
    {
        return Number::currency($this->amount, in: 'USD', locale: $this->locale);
    }

    /**
     * Format the amount in the specified currency.
     *
     * @param string $currency The currency code (e.g., 'USD', 'EUR').
     * @return string The formatted currency string.
     */
    public function in(string $currency): string
    {
        return Number::currency($this->amount, in: $currency, locale: $this->locale);
    }
}
