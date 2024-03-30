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

namespace Asmblah\FastCgi\Exception;

use RuntimeException;

/**
 * Class ProcessException.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProcessException extends RuntimeException implements ExceptionInterface
{
}
