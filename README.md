# Laravel number helper

# Why
Laravel number helper is a package that provides a set of helper functions to work with numbers in Laravel. As of now, [Laravel](https://github.com/laravel/framework/discussions/42941) does not provide a helper function to work with numbers. This package aims to fill that gap.

# Installation
You can install the package via composer:

```bash
composer require silaswint/laravel-number-helper
```

# Usage
The package provides the following helper function:

- `number(...)`

The `number(...)` function can be concatenated with the following methods:

- `->add(number)`
- `->subtract(number)`
- `->multiply(number)`
- `->divide(number)`
- `->round(number)`
- `->min(number)`
- `->max(number)`
etc.

If you want to extend the functionality of the `number(...)` function, you can do this:

```php
    namespace Silaswint\LaravelNumberHelper\App\Number;
    namespace App\Models\Voucher; // your model
    namespace App\Models\Locale; // your model

    // mixin the number helper
    Number::macro('applyDiscount', function (?Voucher $voucher): Number {
        /** @var Number $this */
        $this->value = $voucher?->applyDiscount($this->value) ?? $this->value;

        return $this;
    });

    // Set default locale (you could do this in AppServiceProvider -> boot method)
    Number::setLocale(function () {
        return Locale::current()?->code ?? app()->getLocale();
    });
```
