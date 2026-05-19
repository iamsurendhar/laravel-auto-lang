# Laravel Auto Lang

Automatically scan Laravel project translation strings and append missing translations to `resources/lang/en.json`.

## Features

- Scan `__()` translations
- Scan `trans()` translations
- Automatically generate `lang/en.json`
- Supports Laravel 10, 11, and 12
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

{{ trans('Profile') }}
```

Controller:

```php
return __('Welcome Back');
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

---

## Update Package

```bash
composer update nativecode/laravel-auto-lang
```

---

## License

MIT
