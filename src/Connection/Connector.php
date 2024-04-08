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

namespace Asmblah\FastCgi\Connection;

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\SocketConnections\Defaults;
use hollodotme\FastCGI\SocketConnections\UnixDomainSocket;

/**
 * Class Connector.
 *
 * Connects to the FastCGI server over a Unix domain socket.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Connector implements ConnectorInterface
{
    public function __construct(
        private readonly int $connectTimeout = Defaults::CONNECT_TIMEOUT,
        private readonly int $readWriteTimeout = Defaults::READ_WRITE_TIMEOUT
    ) {
    }

    /**
     * @inheritDoc
     */
    public function connect(string $socketPath): ConnectionInterface
    {
        $fastCgiClient = new Client();
        $fastCgiConnection = new UnixDomainSocket(
            $socketPath,
            $this->connectTimeout,
            $this->readWriteTimeout
        );

        return new Connection($fastCgiClient, $fastCgiConnection);
    }
}
