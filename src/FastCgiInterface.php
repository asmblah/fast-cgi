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

use Asmblah\FastCgi\Session\SessionInterface;

/**
 * Interface FastCgiInterface.
 *
 * Creates FastCGI sessions where both the server and client are encapsulated and managed.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FastCgiInterface
{
    /**
     * Starts a FastCGI session.
     */
    public function start(): SessionInterface;
}
