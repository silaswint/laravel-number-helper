<?php

namespace Silaswint\LaravelNumberHelper\App\Number;

use Closure;
use ReflectionFunction;
use Silaswint\LaravelNumberHelper\App\Currency\Currency;

class Number
{
    use \Illuminate\Support\Traits\Macroable;

    /**
     * @var string|Closure<string> The default locale.
     */
    private static string|Closure $locale = 'de';

    public function __construct(
        public float|int $value,
        ?string $locale = null,
    ) {
        // override default locale
        self::$locale = $locale ?? self::$locale;
    }

    public function evaluate(mixed $value, array $namedInjections = [], array $typedInjections = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        $dependencies = [];

        foreach ((new ReflectionFunction($value))->getParameters() as $parameter) {
            // @todo maybe add support for named and typed injections here
            // $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter, $namedInjections, $typedInjections);
        }

        return $value(...$dependencies);
    }

    /**
     * Set the default locale.
     *
     * @param string|Closure<string> $locale
     */
    public static function setLocale(Closure|string $locale): void
    {
        self::$locale = $locale;
    }

    /**
     * Create a new Number instance.
     */
    public static function make($value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        return new static($value);
    }

    /**
     * Force a value to be a number by defining a fallback value.
     */
    public static function forceNumber(mixed $value, float|int $onError = 0): self
    {
        if (is_numeric($value)) {
            return new static($value);
        }

        if ($value instanceof self) {
            return $value;
        }

        return new static($onError);
    }

    /**
     * Add a number to the current value.
     */
    public function add(float|int|self $number): static
    {
        $this->value += self::unpack($number);

        return $this;
    }

    /**
     * Unpack the number. If it is an instance of Number, return the value.
     */
    private static function unpack(float|int|self $number): float|int
    {
        return $number instanceof self ? $number->getValue() : $number;
    }

    /**
     * Subtract a number from the current value.
     */
    public function subtract(float|int|self $number): static
    {
        $this->value -= self::unpack($number);

        return $this;
    }

    /**
     * Multiply the current value by a number.
     */
    public function multiply(float|int|self $number): static
    {
        $this->value *= self::unpack($number);

        return $this;
    }

    /**
     * Divide the current value by a number.
     */
    public function divide(float|int|self $number): static
    {
        $number = self::unpack($number);

        if ($number == 0) {
            throw new \InvalidArgumentException('Division by zero is not allowed.');
        }

        $this->value /= $number;

        return $this;
    }

    /**
     * Format the number with a specific number of decimals, decimal point and thousands separator.
     */
    public function format(int $decimals = 2, string $decimalPoint = '.', string $thousandsSeparator = ','): string
    {
        return number_format($this->value, $decimals, $decimalPoint, $thousandsSeparator);
    }

    /**
     * Format the number with the default locale.
     */
    public function formatLocale(): false|string
    {
        /** @var string $locale */
        $locale = $this->evaluate(self::$locale);
        return \Illuminate\Support\Number::format($this->value, locale: $locale);
    }

    /**
     * Check if the number is positive.
     */
    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    /**
     * Check if the number is negative.
     */
    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    /**
     * Check if the number is zero.
     */
    public function isZero(): bool
    {
        return $this->value === 0 || $this->value === 0.0;
    }

    /**
     * Check if the number is absolute, i.e. positive.
     */
    public function absolute(): static
    {
        $this->value = abs($this->value);

        return $this;
    }

    /**
     * Get the value of the number.
     */
    public function getValue(): float|int
    {
        return $this->value;
    }

    /**
     * Round the number to a specific precision and mode.
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): static
    {
        $this->value = round($this->value, $precision, $mode);

        return $this;
    }

    /**
     * Get the lowest between the current value and another number.
     */
    public function min(int|float|self $int): static
    {
        $int = self::unpack($int);
        $this->value = min($this->value, $int);

        return $this;
    }

    /**
     * Get the maximum number between the current value and another number.
     */
    public function max(int|float|self $int): static
    {
        $int = self::unpack($int);
        $this->value = max($this->value, $int);

        return $this;
    }

    /**
     * Return the number as a currency.
     */
    public function currency(): Currency
    {
        /** @var string $locale */
        $locale = $this->evaluate(self::$locale);
        return new Currency($this->value, $locale);
    }

    /**
     * Return the number as a human-readable string.
     */
    public function humanReadable(): string
    {
        return rtrim(rtrim(number_format($this->value, 2, '.', ''), '0'), '.');
    }

    public function __toString(): string
    {
        return '' . $this->value;
    }
}
