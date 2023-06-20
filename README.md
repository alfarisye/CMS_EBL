# CMS Dashboard

## Description

Welcome to CMS Dashboard repository!.

### Server requirements
- PHP 7.4 and Apache.
- MySQL.
- OpenSSL 1.1.1h.
- LDAP extension for php.

### Libraries
- [PhpMailer](https://github.com/PHPMailer/PHPMailer).
- [CodeIgniter 4](https://codeigniter.com/user_guide/index.html).

### Development Steps
- Clone this repository.
- Run `composer install`, then `composer update` if needed.
- Run `php spark migrate` or `php spark migrate:rollback`.
- Run `php spark db:seed DataSeeder`.
- Run development server using `php spark serve`.

### Known Errors:
If you get something like :

> PHP Fatal error:  Uncaught TypeError: Argument 1 passed to CodeIgniter\CLI\Console::__construct() must be an instance of CodeIgniter\CodeIgniter, int given, called in /home/anggakawa/cms-baru/spark on line 48 and defined in /vendor/codeigniter4/framework/system/CLI/Console.php:29
Stack trace:
#0 spark(48): CodeIgniter\CLI\Console->__construct()
#1 {main}
  thrown in vendor/codeigniter4/framework/system/CLI/Console.php on line 29. 

Please proceed to do this:

- `composer update`
- `cp vendor/codeigniter4/framework/public/index.php public/index.php`
- `cp vendor/codeigniter4/framework/spark .`

### Todos:
- [ ] create testing script.
- [ ] bypass LDAP only for development environment.
- [ ] github action for development server.

### credits
- Myself.
- [Malik](https://github.com/AkbarMaliki).
- [Alfa](https://github.com/alfarisye).