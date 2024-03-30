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

namespace Asmblah\FastCgi\Session;

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Exceptions\ConnectException;
use hollodotme\FastCGI\Exceptions\TimedoutException;
use hollodotme\FastCGI\Exceptions\WriteFailedException;
use hollodotme\FastCGI\Interfaces\ComposesRequestContent;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

/**
 * Interface SessionInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SessionInterface
{
    /**
     * Fetches the FastCGI Client for the session.
     */
    public function getClient(): Client;

    /**
     * Fetches the FastCGI Connection for the session.
     */
    public function getConnection(): ConfiguresSocketConnection;

    /**
     * Fetches the www base directory for the session.
     */
    public function getWwwDir(): string;

    /**
     * Quits the FastCGI session, ending the worker process(es).
     */
    public function quit(): void;

    /**
     * Makes a GET request to the FastCGI server.
     *
     * @param array<string, mixed> $queryStringArguments
     * @throws TimedoutException
     * @throws WriteFailedException
     * @throws ConnectException
     */
    public function sendGetRequest(
        string $scriptFilename,
        string $requestUri,
        array $queryStringArguments
    ): ProvidesResponseData;

    /**
     * Makes a POST request to the FastCGI server.
     *
     * @param array<string, mixed> $queryStringArguments
     * @throws TimedoutException
     * @throws WriteFailedException
     * @throws ConnectException
     */
    public function sendPostRequest(
        string $scriptFilename,
        string $requestUri,
        array $queryStringArguments,
        ComposesRequestContent $requestContent
    ): ProvidesResponseData;

    /**
     * Makes a generic request to the FastCGI server.
     *
     * @throws TimedoutException
     * @throws WriteFailedException
     * @throws ConnectException
     */
    public function sendRequest(ProvidesRequestData $request): ProvidesResponseData;
}
