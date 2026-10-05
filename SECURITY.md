# Security Policy

## Reporting a vulnerability
Please do **not** open a public issue. Use GitHub's
"Report a vulnerability" button (Security tab) or email the maintainer.

## Secrets
This theme never stores secrets in the repository.
The Kavenegar SMS API key should be defined in `wp-config.php`:

```php
define('VELENTO_KAVENEGAR_API_KEY', 'your-key-here');
```

If you do not define it, the key can be entered in the admin panel
(Settings -> SMS Login) and is stored in the WordPress database.
