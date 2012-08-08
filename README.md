# FS-Homepage Fachschaft Informatik Uni Konstanz

## Setup

- Clone Repository
- Create `./lconf/local-config.php` in project root

```php
<?php
define('DB_NAME', 'DATABASE');
define('DB_USER', 'USERNAME');
define('DB_PASSWORD', 'SECRETPASSWORD');
define('DB_HOST', 'localhost:PORT');
?>
```

- Import database from dump (drop me an email)