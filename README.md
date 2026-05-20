# Laravel Auto Lang

Automatically scan Laravel translation strings and append missing translations to `resources/lang/en.json`.

## Features

- Scan `__()` translations in PHP and Blade files
- Scan `trans()` translations in PHP/controllers
- Scan `t()` translations in JS/TS/JSX/TSX
- Supports local variable translation detection
- Automatically generate `lang/en.json`
- Recursive project scanning
- Smart folder exclusions
- Supports Laravel 10, 11, 12, and 13

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

## PHP / Blade Example

```php
{{ __('Dashboard') }}

{{ __('Plugins') }}

return trans('Welcome Back');
```

---

## React / TSX Example

```tsx
t("Login");

t("Register");

t(`Dashboard`);
```

---

## Local Variable Example

```tsx
const title = "Settings";

t(title);
```

Generated:

```json
{
  "Dashboard": "Dashboard",
  "Plugins": "Plugins",
  "Welcome Back": "Welcome Back",
  "Login": "Login",
  "Register": "Register",
  "Settings": "Settings"
}
```

---

## Supported File Types

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
Added: Plugins
Added: Welcome Back
Added: Login
Added: Settings
Translation scan completed.
```

---

## License

MIT
