# COMP1841

## Environment

The application defaults to production-safe PHP error handling. Set the environment before starting the local server when visible errors are needed during development:

```sh
APP_ENV=development php -S localhost:8000 -t public
```

In production, leave `APP_ENV` unset or set it to `production`. PHP errors are logged but are not displayed to visitors.

When using the PHP built-in server with XAMPP on macOS, point PDO at the XAMPP MySQL socket if `localhost` reports “No such file or directory”:

```sh
APP_ENV=development php -d pdo_mysql.default_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock -S localhost:8000 -t public
```

Future improvement: a notification system could be added later to alert students when someone replies to their question or when a discussion is marked as solved.
