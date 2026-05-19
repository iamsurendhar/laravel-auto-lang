# Laravel Auto Lang

Automatically scan Laravel project translation strings and append missing values to `resources/lang/en.json`.

## Installation

```bash
composer require nativecode/laravel-auto-lang
```

## Usage

Run command:

```bash
php artisan auto-lang:scan
```

Example:

```php
__('Dashboard');

trans('Profile');
```

Generated:

```json
{
    "Dashboard": "Dashboard",
    "Profile": "Profile"
}
```
