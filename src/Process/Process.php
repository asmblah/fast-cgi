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
use hollodotme\FastCGI\Exceptions\FastCGIClientException;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\Requests\GetRequest;

/**
 * Class Process.
 *
 * Represents the FastCGI server process (or main process).
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Process implements ProcessInterface
{
    /**
     * @var resource|null
     */
    private $processResource;

    /**
     * @param resource $processResource
     * @param array<resource> $pipes
     */
    public function __construct(
        $processResource,
        private array $pipes
    ) {
        $this->processResource = $processResource;
    }

    /**
     * @inheritDoc
     */
    public function quit(): void
    {
        if ($this->processResource === null) {
            throw new ProcessException('Process has already been exited');
        }

        fclose($this->pipes[0]);
        fclose($this->pipes[1]);
        fclose($this->pipes[2]);

        $status = proc_get_status($this->processResource);

        if (!$status['running']) {
            proc_close($this->processResource);

            throw new ProcessException(
                'php-cgi process had stopped unexpectedly, exit code was ' . $status['exitcode']
            );
        }

        proc_terminate($this->processResource, SIGTERM);
        usleep(100 * 1000);
        proc_terminate($this->processResource, SIGKILL);

        proc_close($this->processResource);

        $this->processResource = null;
        $this->pipes = [];
    }

    /**
     * @inheritDoc
     */
    public function waitUntilReady(Client $fastCgiClient, ConfiguresSocketConnection $fastCgiConnection): void
    {
        // Wait for the FastCGI server to be ready to receive FastCGI requests.
        for (;;) {
            try {
                $response = $fastCgiClient->sendRequest(
                    $fastCgiConnection,
                    new GetRequest('/', '')
                );
            } catch (FastCGIClientException) {
                $response = null;
            }

            if ($response && $response->getHeaderLine('Status') === '404 Not Found') {
                break;
            }

            $status = proc_get_status($this->processResource);

            if (!$status['running']) {
                throw new ProcessException(
                    sprintf(
                        'FastCGI process exited unexpectedly with signal %d, stdout: "%s", stderr: "%s"',
                        $status['termsig'],
                        stream_get_contents($this->pipes[1]),
                        stream_get_contents($this->pipes[2])
                    )
                );
            }

            usleep(100000);
        }
    }
}
