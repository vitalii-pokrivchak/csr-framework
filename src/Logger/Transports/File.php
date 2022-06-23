<?php

namespace Csr\Framework\Logger\Transports;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

class File extends Transport
{
    protected string $path;

    /**
     * File constructor.
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $this->path = $container->get('logger.path');
    }

    public function output(string $message, string $level)
    {
        if (!is_dir($this->path) && is_writable($this->path)) {
            if (!file_exists($this->path)) {
                touch($this->path);
            }

            $stream = fopen($this->path, 'w');
            if ($stream !== false) {
                fwrite($stream, $message);
                fclose($stream);
            }
        }
    }
}
