# Laravel Auto Lang

Automatically scan Laravel translation strings and append missing translations to `resources/lang/en.json`.

## Features

- Scan `__()` translations in Blade files
- Scan `trans()` translations in PHP/controllers
- Automatically generate `lang/en.json`
- Supports Laravel 10, 11, 12, and 13
- Simple Artisan command

---

## Installation

```bash
composer require nativecode/laravel-auto-lang
```

---

## Usage

Run the scan command:

```bash
php artisan auto-lang:scan
```

---

## Example

Blade:

```blade
{{ __('Dashboard') }}

{{ __('Profile') }}
```

Controller:

```php
return trans('Welcome Back');
```

Generated:

```json
{
  "Dashboard": "Dashboard",
  "Profile": "Profile",
  "Welcome Back": "Welcome Back"
}
```

---

## Scanned Locations

The package scans:

```txt
app/
resources/views/
```

---

## Supported Laravel Versions

| Laravel Version | Supported |
| --------------- | --------- |
| 10.x            | Yes       |
| 11.x            | Yes       |
| 12.x            | Yes       |
| 13.x            | Yes       |

---

## Update Package

```bash
composer update nativecode/laravel-auto-lang
```

---

## Run Scan Again

```bash
php artisan auto-lang:scan
```

---

## License

MIT
