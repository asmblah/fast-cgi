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

namespace Asmblah\FastCgi\Tests\Unit;

use Asmblah\FastCgi\Connection\ConnectionInterface;
use Asmblah\FastCgi\Connection\ConnectorInterface;
use Asmblah\FastCgi\FastCgi;
use Asmblah\FastCgi\Launcher\LauncherInterface;
use Asmblah\FastCgi\Process\ProcessInterface;
use Asmblah\FastCgi\Tests\AbstractTestCase;
use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use Mockery\MockInterface;

/**
 * Class FastCgiTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgiTest extends AbstractTestCase
{
    private MockInterface&ConnectionInterface $connection;
    private MockInterface&ConnectorInterface $connector;
    private MockInterface&Client $fastCgiClient;
    private MockInterface&ConfiguresSocketConnection $fastCgiConnection;
    private FastCgi $fastCgi;
    private MockInterface&LauncherInterface $launcher;
    private MockInterface&ProcessInterface $process;

    public function setUp(): void
    {
        $this->connector = mock(ConnectorInterface::class);
        $this->fastCgiClient = mock(Client::class);
        $this->fastCgiConnection = mock(ConfiguresSocketConnection::class);
        $this->connection = mock(ConnectionInterface::class, [
            'getClient' => $this->fastCgiClient,
            'getConnection' => $this->fastCgiConnection,
        ]);
        $this->launcher = mock(LauncherInterface::class);
        $this->process = mock(ProcessInterface::class, [
            'waitUntilReady' => null,
        ]);

        $this->connector->allows()
            ->connect('/path/to/my.sock')
            ->andReturn($this->connection)
            ->byDefault();
        $this->launcher->allows()
            ->launch('/my/base/dir', '/path/to/my.sock')
            ->andReturn($this->process);

        $this->fastCgi = new FastCgi(
            baseDir: '/my/base/dir',
            wwwDir: 'public/www',
            socketPath: '/path/to/my.sock',
            launcher: $this->launcher,
            connector: $this->connector
        );
    }

    public function testStartReturnsACorrectlyConstructedSession(): void
    {
        $session = $this->fastCgi->start();

        static::assertSame('/my/base/dir/public/www', $session->getWwwDir());
        static::assertSame($this->fastCgiClient, $session->getClient());
        static::assertSame($this->fastCgiConnection, $session->getConnection());
    }

    public function testStartWaitsForProcessToBeReadyToReceiveFastCgiRequests(): void
    {
        $this->process->expects()
            ->waitUntilReady($this->fastCgiClient, $this->fastCgiConnection)
            ->once();

        $this->fastCgi->start();
    }
}
