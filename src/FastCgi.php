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

namespace Asmblah\FastCgi;

use Asmblah\FastCgi\Connection\Connector;
use Asmblah\FastCgi\Connection\ConnectorInterface;
use Asmblah\FastCgi\Launcher\LauncherInterface;
use Asmblah\FastCgi\Session\Session;
use Asmblah\FastCgi\Session\SessionInterface;

/**
 * Class FastCgi.
 *
 * Creates FastCGI sessions where both the server and client are encapsulated and managed.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FastCgi implements FastCgiInterface
{
    private readonly string $baseDir;
    private readonly string $wwwDir;

    public function __construct(
        string $baseDir,
        string $wwwDir,
        private readonly string $socketPath,
        private readonly LauncherInterface $launcher,
        private readonly ConnectorInterface $connector = new Connector()
    ) {
        $baseDir = rtrim($baseDir, '/');
        $wwwDir = trim($wwwDir, '/');

        $this->baseDir = $baseDir;
        $this->wwwDir = $baseDir . '/' . $wwwDir;
    }

    /**
     * @inheritDoc
     */
    public function start(): SessionInterface
    {
        $process = $this->launcher->launch($this->baseDir, $this->socketPath);

        $connection = $this->connector->connect($this->socketPath);
        $fastCgiClient = $connection->getClient();
        $fastCgiConnection = $connection->getConnection();

        $process->waitUntilReady($fastCgiClient, $fastCgiConnection);

        return new Session(
            $process,
            $fastCgiClient,
            $fastCgiConnection,
            $this->wwwDir
        );
    }
}
