<?php

namespace Csr\Framework\Logger\Transports;

abstract class Transport
{
    /**
     * Describe method for output logs
     *
     * @param string $message
     * @param string $level
     *
     * @return void
     */
    abstract public function output(string $message, string $level);
}
