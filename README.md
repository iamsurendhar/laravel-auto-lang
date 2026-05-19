# Laravel Auto Lang

Automatically append missing Laravel translation strings to `resources/lang/en.json` when using `__()` or `trans()`.

## Features

- Auto detect missing translations
- Automatically write to `lang/en.json`
- Supports `__()` and `trans()`
- Laravel auto-discovery support
- Lightweight and simple

---

## Installation

```bash
composer require nativecode/laravel-auto-lang
```

---

## Usage

Simply use Laravel translations normally:

```php
__('Dashboard');

trans('Profile');
```

If the key does not exist, it will automatically be added to:

```txt
resources/lang/en.json
```

Example generated file:

```json
{
  "Dashboard": "Dashboard",
  "Profile": "Profile"
}
```

---

## Configuration

Currently no configuration is required.

---

## Example

```php
return __('Welcome Back');
```

Auto generated:

```json
{
  "Welcome Back": "Welcome Back"
}
```

---

## Supported Laravel Versions

| Laravel | Supported |
| ------- | --------- |
| 10.x    | Yes       |
| 11.x    | Yes       |
| 12.x    | Yes       |

---

## Security

Do not enable automatic file writing in production environments unless intended.

---

## License

MIT
