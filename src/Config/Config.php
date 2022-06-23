<?php

namespace Csr\Framework\Config;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use DI\Container;
use Dotenv\Dotenv;

class Config
{
    /**
     * Constructor
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $path = $container->get('config.path');
        $dotenv = Dotenv::createUnsafeImmutable($path);
        $dotenv->load();
    }

    /**
     * Get variable by key
     *
     * Return value if it exists otherwise return default value
     * @param string $key variable name
     * @param mixed $default default value
     *
     * @return mixed|array|null
     */
    public function get(string $key = '', $default = null): ?array
    {
        if ($key == '') {
            return $_ENV;
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        return $default;
    }

    /**
     * Get value by key or throw exception when not found
     *
     * @param string $key variable name
     *
     * @return mixed
     * @throws Exception
     */
    public function getOrThrow(string $key)
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        throw new Exception('Cannot find value in config');
    }
}
