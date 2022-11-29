Session
========

The `Session` class used for our projects. It stores sessions in tha database using the `AbstractDatabase` class.


Setup
-----
Create tha table and columns.

```mysql
create table sessions
(
    id     varchar(128) not null primary key,
    access int(10) unsigned default null,
    `data` text
) collate = utf8mb4_unicode_ci;

```

Usage
-----

```php
use DeLoachTech\Database\AbstractDatabase;
use DeLoachTech\Session\Session;

// Initilization Layer //

$db = new Database($host, $user, $password, $database, $charset); // extends the AbstractDatabase

// Using two databases. One for development and the other for production.
new Session($db, $_SERVER['SERVER_NAME'] == 'localhost' ? 'app_dev' : 'app_prod');

// Use PHP $_SESSION values as normal.
```