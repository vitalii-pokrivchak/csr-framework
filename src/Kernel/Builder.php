<?php

namespace Csr\Framework\Kernel;

use Csr\Framework\Config\Config;
use Csr\Framework\Http\Request;
use Csr\Framework\Http\Response;
use Csr\Framework\Http\Session;
use Csr\Framework\Logger\Logger;
use Csr\Framework\Router\Router;
use Csr\Framework\Template\TemplateProvider;

use function \DI\get;

class Builder
{
    /**
     * @var array|string[]
     */
    protected array $definitions = [
        'config.path' => '',
        'logger.path' => '',
        'logger.transport' => 'console',
        'logger.format' => '[{level}] - {time} - {message}',
        'template.cacheEnabled' => false,
        'template.cache' => ''
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->definitions[TemplateProvider::class] = null;
    }

    /**
     * Add objects , interfaces to DI container
     *
     * @param string $name maybe classname or interface name of string
     * @param mixed $target object or class string
     *
     * @return self
     */
    public function with(string $name, $target): self
    {
        if (is_string($target)) {
            if (class_exists($target)) {
                $this->definitions[$name] = get($target);
            }
        } else {
            $this->definitions[$name] = $target;
        }
        return $this;
    }

    /**
     * Configure configuration for framework
     *
     * @param string $path location to folder or file
     * @param string $config class extended from Config
     *
     * @return self
     */
    public function withConfig(string $path, string $config = ''): self
    {
        $this->definitions['config.path'] = $path;
        if ($config !== '') {
            if (class_exists($config)) {
                if (is_subclass_of($config, Config::class)) {
                    $this->definitions[Config::class] = get($config);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $transport log transport
     * @param string $format message format
     * @param string $path location to logs if you use File transport
     * @param string $log class extended from Logger
     * @return self
     */
    public function withLog(
        string $transport = 'console',
        string $format = '[{level}] - {time} - {message}',
        string $path = '',
        string $log = Logger::class
    ): self
    {
        $this->definitions['logger.transport'] = $transport;
        $this->definitions['logger.format'] = $format;
        $this->definitions['logger.path'] = $path;

        if (class_exists($log)) {
            if (is_subclass_of($log, Logger::class)) {
                $this->definitions[Logger::class] = get($log);
            }
        }
        return $this;
    }

    /**
     * Configure template engine for framework
     *
     * @param string $path
     * @param string $cache
     * @param string $template class extended from TemplateProvider
     * @param bool $cacheEnabled
     * @return self
     */
    public function withTemplate(string $path, string $template, string $cache = '', bool $cacheEnabled = false): self
    {
        $this->definitions['template.path'] = $path;
        $this->definitions['template.cache'] = $cache;
        $this->definitions['template.cacheEnabled'] = $cacheEnabled;

        if (class_exists($template)) {
            if (is_subclass_of($template, TemplateProvider::class)) {
                $this->definitions[TemplateProvider::class] = get($template);
            }
        }
        return $this;
    }

    /**
     * Configure HTTP for framework
     *
     * @param string $request class extended from Request
     * @param string $response class extended from Response
     *
     * @return self
     */
    public function withHttp(string $request, string $response): self
    {
        if (class_exists($request) && class_exists($response)) {
            if (is_subclass_of($request, Request::class) && is_subclass_of($response, Response::class)) {
                $this->definitions[Request::class] = get($request);
                $this->definitions[Response::class] = get($response);
            }
        }
        return $this;
    }

    /**
     * Configure router for framework
     *
     * @param string $router class extended from Router
     *
     * @return self
     */
    public function withRouter(string $router): self
    {
        if (class_exists($router)) {
            if (is_subclass_of($router, Router::class)) {
                $this->definitions[Router::class] = get($router);
            }
        }
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function withSession(array $options = []): self
    {
        $this->definitions[Session::class] = Session::start($options);
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
