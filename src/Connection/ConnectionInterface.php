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
 * Interface ConnectionInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConnectionInterface
{
    /**
     * Fetches the FastCGI client.
     */
    public function getClient(): Client;

    /**
     * Fetches the FastCGI connection.
     */
    public function getConnection(): ConfiguresSocketConnection;
}
