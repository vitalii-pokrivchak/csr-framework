<?php

namespace Csr\Framework\Config;

use Csr\Framework\Common\Configurable;
use Csr\Framework\Logger\Logger;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Exception;

class Config
{
    protected Container $container;

    /**
     * Constructor
     * @param Container $container
     * @param Logger $logger
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container, Logger $logger)
    {
        $this->container = $container;
        $path = $container->get('config.path');
        try {
            $dotenv = Dotenv::createUnsafeImmutable($path);
            $dotenv->load();
        } catch (InvalidPathException $ex) {
            $logger->warning('Cannot find env file');
        }
    }

    /**
     * Get variable by key
     *
     * Return value if it exists otherwise return default value
     * @param string $key variable name
     * @param mixed $default default value
     *
     * @return mixed
     */
    public function get(string $key = '', $default = null)
    {
        if ($key == '') {
            return $_ENV;
        }

        $key = strtoupper($key);

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
        $key = strtoupper($key);

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        throw new Exception('Cannot find value in config');
    }

    /**
     * Check value if exists
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $_ENV);
    }

    /**
     * @param Configurable|string $object
     * @return Configurable|object
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function forObject($object)
    {
        if (is_string($object) && class_exists($object)) {
            $object = $this->container->get($object);
        }

        if ($object instanceof Configurable) {
            $object->configure($this);
        }

        return $object;
    }
}
