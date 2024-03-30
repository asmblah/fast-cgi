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

use Asmblah\FastCgi\Process\ProcessInterface;

/**
 * Interface LauncherInterface.
 *
 * Handles launching of a specific type of FastCGI server process, e.g. `php-cgi` or `php-fpm`.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LauncherInterface
{
    /**
     * Launches the FastCGI server process.
     */
    public function launch(string $baseDir, string $socketPath): ProcessInterface;
}
