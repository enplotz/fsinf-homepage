# FS-Homepage Fachschaft Informatik Uni Konstanz

current Wordpress version: `3.4.1`

## Requirements

- PHP version `5.2.4` or greater
- MySQL version `5.0` or greater


## Setup

- Clone Repository
- Create `./lconf/local-config.php` in project root

```php
<?php
define('DB_NAME', 'DATABASE');
define('DB_USER', 'USERNAME');
define('DB_PASSWORD', 'SECRETPASSWORD');
define('DB_HOST', 'localhost:PORT');

// Long strings with varying characters such as
// ?9; $[VxhtZ_-,{QFCGe=tm_Cd)opp-Ik2C/7-8*g+t] %i~lu8@0lChhNj/1S-9
define('AUTH_KEY',         'STRING');
define('SECURE_AUTH_KEY',  'STRING');
define('LOGGED_IN_KEY',    'STRING');
define('NONCE_KEY',        'STRING');
define('AUTH_SALT',        'STRING');
define('SECURE_AUTH_SALT', 'STRING');
define('LOGGED_IN_SALT',   'STRING');
define('NONCE_SALT',       'STRING');

?>
```

- Import database from dump (drop me an email)