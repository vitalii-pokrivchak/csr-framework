<?php

namespace Csr\Framework\Http;

abstract class Controller
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
     * Finish request
     *
     * This is a system method
     * @param $value
     * @return mixed
     */
    abstract public function finish($value);
}
