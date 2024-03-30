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

use Asmblah\FastCgi\Process\ProcessInterface;
use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Interfaces\ComposesRequestContent;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use hollodotme\FastCGI\Requests\GetRequest;
use hollodotme\FastCGI\Requests\PostRequest;

/**
 * Class Session.
 *
 * Represents a running FastCGI server and a client for communicating with it.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Session implements SessionInterface
{
    public function __construct(
        private readonly ProcessInterface $process,
        private readonly Client $fastCgiClient,
        private readonly ConfiguresSocketConnection $fastCgiConnection,
        private readonly string $wwwDir
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getClient(): Client
    {
        return $this->fastCgiClient;
    }

    /**
     * @inheritDoc
     */
    public function getConnection(): ConfiguresSocketConnection
    {
        return $this->fastCgiConnection;
    }

    /**
     * @inheritDoc
     */
    public function getWwwDir(): string
    {
        return $this->wwwDir;
    }

    /**
     * @inheritDoc
     */
    public function quit(): void
    {
        $this->process->quit();
    }

    /**
     * @inheritDoc
     */
    public function sendGetRequest(
        string $scriptFilename,
        string $requestUri,
        array $queryStringArguments
    ): ProvidesResponseData {
        $request = new GetRequest($this->wwwDir . '/' . $scriptFilename, '');
        $request->setRequestUri($requestUri);
        $request->setCustomVar('QUERY_STRING', http_build_query($queryStringArguments));

        return $this->fastCgiClient->sendRequest($this->fastCgiConnection, $request);
    }

    /**
     * @inheritDoc
     */
    public function sendPostRequest(
        string $scriptFilename,
        string $requestUri,
        array $queryStringArguments,
        ComposesRequestContent $requestContent
    ): ProvidesResponseData {
        $request = PostRequest::newWithRequestContent(
            $this->wwwDir . '/' . $scriptFilename,
            $requestContent
        );
        $request->setRequestUri($requestUri);
        $request->setCustomVar('QUERY_STRING', http_build_query($queryStringArguments));

        return $this->fastCgiClient->sendRequest($this->fastCgiConnection, $request);
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(ProvidesRequestData $request): ProvidesResponseData
    {
        return $this->fastCgiClient->sendRequest($this->fastCgiConnection, $request);
    }
}
