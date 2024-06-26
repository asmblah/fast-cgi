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

namespace Asmblah\FastCgi\Launcher;

use Asmblah\FastCgi\Exception\ProcessException;
use Asmblah\FastCgi\Process\Process;
use Asmblah\FastCgi\Process\ProcessInterface;

/**
 * Class PhpCgiLauncher.
 *
 * Handles launching of a `php-cgi` FastCGI server process.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PhpCgiLauncher implements LauncherInterface
{
    public function __construct(
        private readonly string $phpCgiBinaryPath,
        private readonly string $extraArguments = ''
    ) {
    }

    /**
     * @inheritDoc
     */
    public function launch(string $baseDir, string $socketPath): ProcessInterface
    {
        $descriptorSpec = [
            0 => ['pipe', 'r'], // Stdin.
            1 => ['pipe', 'w'], // Stdout.
            2 => ['pipe', 'w'], // Stderr.
        ];

        // Spawn a long-running FastCGI server for handling FastCGI requests.
        $processResource = proc_open(
            sprintf(
                'PHP_FCGI_CHILDREN=0 PHP_FCGI_MAX_REQUESTS=1000 exec %s -d open_basedir=%s -b %s%s',
                escapeshellarg($this->phpCgiBinaryPath),
                escapeshellarg($baseDir),
                escapeshellarg($socketPath),
                $this->extraArguments !== '' ? ' ' . $this->extraArguments : ''
            ),
            $descriptorSpec,
            $pipes
        );

        if ($processResource === false) {
            throw new ProcessException('Failed to start FastCGI server.');
        }

        return new Process($processResource, $pipes);
    }
}
