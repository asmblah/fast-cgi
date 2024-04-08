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

namespace Asmblah\FastCgi\Tests\Unit\Connection;

use Asmblah\FastCgi\Connection\Connector;
use Asmblah\FastCgi\Tests\AbstractTestCase;
use hollodotme\FastCGI\SocketConnections\Defaults;

/**
 * Class ConnectorTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConnectorTest extends AbstractTestCase
{
    private Connector $connector;

    public function setUp(): void
    {
        $this->connector = new Connector(1234, 5678);
    }

    public function testConnectReturnsACorrectlyConstructedConnection(): void
    {
        $connection = $this->connector->connect('/path/to/my_socket.sock');

        static::assertSame('unix:///path/to/my_socket.sock', $connection->getConnection()->getSocketAddress());
        static::assertSame(1234, $connection->getConnection()->getConnectTimeout());
        static::assertSame(5678, $connection->getConnection()->getReadWriteTimeout());
    }

    public function testConnectUsesClientLibDefaultsByDefault(): void
    {
        $connector = new Connector();

        $connection = $connector->connect('/path/to/my_socket.sock');

        static::assertSame('unix:///path/to/my_socket.sock', $connection->getConnection()->getSocketAddress());
        static::assertSame(Defaults::CONNECT_TIMEOUT, $connection->getConnection()->getConnectTimeout());
        static::assertSame(Defaults::READ_WRITE_TIMEOUT, $connection->getConnection()->getReadWriteTimeout());
    }
}
