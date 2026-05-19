# Laravel Auto Lang

Automatically append missing Laravel translation strings to `resources/lang/en.json` when using `__()` or `trans()`.

## Installation

```bash
composer require nativecode/laravel-auto-lang
```

## Usage

```php
__('Dashboard');

trans('Profile');
```

Missing keys are automatically added to:

```txt
resources/lang/en.json
```
