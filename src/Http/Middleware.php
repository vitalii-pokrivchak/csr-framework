<?php

namespace Csr\Framework\Http;

abstract class Middleware
{
    protected Request $request;
    protected Response $response;

    /**
     * Constructor
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Execute handler for middleware
     *
     * Return true if you want to skip the request and pass it to the controller, otherwise return false
     *
     * @return bool
     */
    abstract public function execute(): bool;
}
