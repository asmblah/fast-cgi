# FastCGI Manager

[![Build Status](https://github.com/asmblah/fast-cgi/actions/workflows/main.yml/badge.svg)](https://github.com/asmblah/fast-cgi/actions?query=workflow%3ACI)

Simplifies management of a real `php-cgi` worker process or `php-fpm` worker pool.

Leverages the excellent [hollodotme/fast-cgi-client][hollodotme/fast-cgi-client] for a [FastCGI][FastCGI] API.

## Why?
Easy testing of a FastCGI application's behaviour across requests: for example,
ensuring that opcache is cleared correctly when expected.

## Usage
```shell
$ composer install --dev asmblah/fast-cgi
```

### Launching a `php-cgi` instance and making a GET request to it:

Use `PhpCgiLauncher` for `php-cgi`.
#### `test.php`

```php
<?php

declare(strict_types=1);

use Asmblah\FastCgi\FastCgi;
use Asmblah\FastCgi\Launcher\PhpCgiLauncher;

require_once __DIR__ . '/vendor/autoload.php';

$baseDir = __DIR__;
$wwwDir = 'www'; // Relative to $baseDir.
$phpCgiBinaryPath = dirname(PHP_BINARY) . '/php-cgi';

$dataDir = $baseDir . '/var/test';
@mkdir($dataDir, 0700, true);
$socketPath = $dataDir . '/php-cgi.test.sock';

$fastCgi = new FastCgi(
    baseDir: $baseDir,
    wwwDir: $wwwDir,
    socketPath: $socketPath,
    launcher: new PhpCgiLauncher($phpCgiBinaryPath)
);
$session = $fastCgi->start();

$response = $session->sendGetRequest(
    'my_script.php',
    '/path/to/my-page',
    [
        'greeting' => 'Hello',
    ]
);

// Will print "Hello from my front controller!".
print $response->getBody() . PHP_EOL;

$session->quit();
```

#### `www/my_script.php`
```php
<?php

declare(strict_types=1);

print ($_GET['greeting'] ?? '(none)') . ' from my front controller!';
```

#### Run
```shell
$ php test.php
Hello from my front controller!
```

### Launching a `php-fpm` instance and making a GET request to it:

Use `PhpFpmLauncher` for `php-fpm`.
#### `test.php`

```php
<?php

declare(strict_types=1);

use Asmblah\FastCgi\FastCgi;
use Asmblah\FastCgi\Launcher\PhpFpmLauncher;

require_once __DIR__ . '/vendor/autoload.php';

$baseDir = __DIR__;
$wwwDir = 'www'; // Relative to $baseDir.
$phpFpmBinaryPath = dirname(PHP_BINARY, 2) . '/sbin/php-fpm';

$dataDir = $baseDir . '/var/test';
@mkdir($dataDir, 0700, true);
$socketPath = $dataDir . '/php-fpm.test.sock';
$logFilePath = $dataDir . '/php-fpm.log';

$configFilePath = $dataDir . '/php-fpm.conf';
file_put_contents($configFilePath, <<<CONFIG
[global]
error_log = $logFilePath

[www]
listen = $socketPath
pm = static
pm.max_children = 1

CONFIG
);

$fastCgi = new FastCgi(
    baseDir: $baseDir,
    wwwDir: $wwwDir,
    socketPath: $socketPath,
    launcher: new PhpFpmLauncher(
        $phpFpmBinaryPath,
        $configFilePath
    )
);
$session = $fastCgi->start();

$response = $session->sendGetRequest(
    'my_script.php',
    '/path/to/my-page',
    [
        'greeting' => 'Hello',
    ]
);

// Will print "Hello from my front controller!".
print $response->getBody() . PHP_EOL;

$session->quit();
```

#### `www/my_script.php`
```php
<?php

declare(strict_types=1);

print ($_GET['greeting'] ?? '(none)') . ' from my front controller!';
```

#### Run
```shell
$ php test.php
Hello from my front controller!
```

## See also
- [FastCGI][FastCGI]
- [hollodotme/fast-cgi-client][hollodotme/fast-cgi-client]

[FastCGI]: https://en.wikipedia.org/wiki/FastCGI
[hollodotme/fast-cgi-client]: https://github.com/hollodotme/fast-cgi-client
