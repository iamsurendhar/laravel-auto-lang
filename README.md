# Laravel Auto Lang

Automatically scan Laravel translation strings and append missing translations to `resources/lang/en.json`.

## Features

- Scan `__()` translations in Blade files
- Scan `trans()` translations in PHP/controllers
- Scan `t()` translations in JS/TS/React/TSX
- Automatically generate `lang/en.json`
- Supports Laravel 10, 11, 12, and 13
- Simple Artisan command
- Recursive project scanning
- Smart folder exclusions

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

### Blade

```blade
{{ __('Dashboard') }}

{{ __('Profile') }}
```

### Controller

```php
return trans('Welcome Back');
```

### React / TSX

```tsx
t("Login");

t("Register");
```

Generated:

```json
{
  "Dashboard": "Dashboard",
  "Profile": "Profile",
  "Welcome Back": "Welcome Back",
  "Login": "Login",
  "Register": "Register"
}
```

---

## Supported File Types

The package scans:

```txt
.php
.blade.php
.js
.ts
.jsx
.tsx
```

---

## Excluded Folders

The package ignores:

```txt
bootstrap/
config/
database/
routes/
storage/
tests/
vendor/
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

## Example Output

```txt
Added: Dashboard
Added: Login
Added: Register
Translation scan completed.
```

---

## License

MIT
