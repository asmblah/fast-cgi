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

namespace Asmblah\FastCgi\Process;

use Asmblah\FastCgi\Exception\ProcessException;
use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;

/**
 * Interface ProcessInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProcessInterface
{
    /**
     * Quits the worker process(es).
     */
    public function quit(): void;

    /**
     * Waits for the FastCGI server process to be ready to receive FastCGI requests.
     *
     * @throws ProcessException When the process exits unexpectedly.
     */
    public function waitUntilReady(Client $fastCgiClient, ConfiguresSocketConnection $fastCgiConnection): void;
}
