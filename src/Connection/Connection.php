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
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;

/**
 * Class Connection.
 *
 * Contains the FastCGI client and connection to the FastCGI server.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Connection implements ConnectionInterface
{
    public function __construct(
        private readonly Client $fastCgiClient,
        private readonly ConfiguresSocketConnection $fastCgiConnection
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getClient(): Client
    {
        return $this->fastCgiClient;
    }

    /**
     * @inheritDoc
     */
    public function getConnection(): ConfiguresSocketConnection
    {
        return $this->fastCgiConnection;
    }
}
