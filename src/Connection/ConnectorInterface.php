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

/**
 * Interface ConnectorInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConnectorInterface
{
    /**
     * Opens a connection to the FastCGI server.
     */
    public function connect(string $socketPath): ConnectionInterface;
}
