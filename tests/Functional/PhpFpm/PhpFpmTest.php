<?php

/*
 * FastCGI Manager.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/asmblah/fast-cgi/
 *
 * Released under the MIT license.
 * https://github.com/asmblah/fast-cgi/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Asmblah\FastCgi\Tests\Functional\PhpFpm;

use Asmblah\FastCgi\FastCgi;
use Asmblah\FastCgi\Launcher\PhpFpmLauncher;
use Asmblah\FastCgi\Session\SessionInterface;
use Asmblah\FastCgi\Tests\AbstractTestCase;
use hollodotme\FastCGI\RequestContents\UrlEncodedFormData;
use hollodotme\FastCGI\Requests\GetRequest;
use hollodotme\FastCGI\Requests\PostRequest;

/**
 * Class PhpFpmTest.
 *
 * Tests launching of a real `php-fpm` process and communication with it over FastCGI.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PhpFpmTest extends AbstractTestCase
{
    private string $baseDir;
    private string $configFilePath;
    private FastCgi $fastCgi;
    private string $phpFpmBinaryPath;
    private SessionInterface $session;
    private string $socketPath;
    private string $wwwDir;

    public function setUp(): void
    {
        $this->baseDir = dirname(__DIR__, 3);
        $this->wwwDir = 'tests/Functional/Fixtures/www';
        $this->phpFpmBinaryPath = dirname(PHP_BINARY, 2) . '/sbin/php-fpm';

        $dataDir = $this->baseDir . '/var/test';
        @mkdir($dataDir, 0777, true);
        $this->socketPath = $dataDir . '/php-fpm.test.sock';
        $logFilePath = $dataDir . '/php-fpm.log';

        $this->configFilePath = $dataDir . '/php-fpm.conf';
        file_put_contents($this->configFilePath, <<<CONFIG
[global]
error_log = $logFilePath

[www]
listen = $this->socketPath
pm = static
pm.max_children = 1

CONFIG
);

        $this->fastCgi = new FastCgi(
            baseDir: $this->baseDir,
            wwwDir: $this->wwwDir,
            socketPath: $this->socketPath,
            launcher: new PhpFpmLauncher(
                $this->phpFpmBinaryPath,
                $this->configFilePath
            )
        );
    }

    public function tearDown(): void
    {
        $this->session->quit();
    }

    public function testCanMakeFastCgiGetRequestViaSendGetRequest(): void
    {
        $this->session = $this->fastCgi->start();

        $response = $this->session->sendGetRequest(
            'get_method_front_controller.php',
            '/path/to/my-page',
            [
                'greeting' => 'Hello',
            ]
        );

        static::assertSame('Hello from the front controller!', $response->getBody());
    }

    public function testCanMakeFastCgiPostRequestViaSendPostRequest(): void
    {
        $this->session = $this->fastCgi->start();

        $response = $this->session->sendPostRequest(
            'post_method_front_controller.php',
            '/path/to/my-page',
            [
                'greeting' => 'Hello',
            ],
            new UrlEncodedFormData(['message' => 'Surprise!'])
        );

        static::assertSame(
            'Hello from the front controller, I had this POSTed: "Surprise!"!',
            $response->getBody()
        );
    }

    public function testCanMakeFastCgiGetRequestViaSendRequest(): void
    {
        $this->session = $this->fastCgi->start();
        $request = new GetRequest($this->session->getWwwDir() . '/get_method_front_controller.php', '');
        $request->setRequestUri('/path/to/my-page');
        $request->setCustomVar('QUERY_STRING', 'greeting=Hello');

        $response = $this->session->sendRequest($request);

        static::assertSame('Hello from the front controller!', $response->getBody());
    }

    public function testCanMakeFastCgiPostRequestViaSendRequest(): void
    {
        $this->session = $this->fastCgi->start();
        $request = PostRequest::newWithRequestContent(
            $this->session->getWwwDir() . '/post_method_front_controller.php',
            new UrlEncodedFormData(['message' => 'Another surprise!'])
        );
        $request->setRequestUri('/path/to/my-page');
        $request->setCustomVar('QUERY_STRING', 'greeting=Hello');

        $response = $this->session->sendRequest($request);

        static::assertSame(
            'Hello from the front controller, I had this POSTed: "Another surprise!"!',
            $response->getBody()
        );
    }

    public function testCanMakeMultipleFastCgiGetRequestsToTheSameWorkerProcess(): void
    {
        $this->session = $this->fastCgi->start();

        $response1 = $this->session->sendGetRequest(
            'get_method_front_controller.php',
            '/path/to/my-page',
            [
                'greeting' => 'Hello',
            ]
        );

        $response2 = $this->session->sendGetRequest(
            'get_method_front_controller.php',
            '/path/to/my-page',
            [
                'greeting' => 'And hello again',
            ]
        );

        static::assertSame('Hello from the front controller!', $response1->getBody());
        static::assertSame('And hello again from the front controller!', $response2->getBody());
    }

    public function testCanProvideAdditionalCommandLineArguments(): void
    {
        $this->fastCgi = new FastCgi(
            baseDir: $this->baseDir,
            wwwDir: $this->wwwDir,
            socketPath: $this->socketPath,
            launcher: new PhpFpmLauncher(
                $this->phpFpmBinaryPath,
                $this->configFilePath,
                '-d my_entry=456'
            )
        );

        $this->session = $this->fastCgi->start();

        $response = $this->session->sendGetRequest(
            'fetch_ini_entry.php',
            '/path/to/my-page',
            [
                'entry' => 'my_entry',
            ]
        );

        static::assertSame('INI entry value: 456', $response->getBody());
    }
}
