<?php

namespace Csr\Framework\Router;

use Csr\Framework\Http\Method;

class Route
{
    protected static array $routes = [];

    /**
     * Describe GET mapping
     *
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function get(string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][Method::GET] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe POST mapping
     *
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function post(string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][Method::POST] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe PUT mapping
     *
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function put(string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][Method::PUT] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe PATCH mapping
     *
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function patch(string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][Method::PATCH] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe DELETE mapping
     *
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function delete(string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][Method::DELETE] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe url mapping
     *
     * @param string $method
     * @param string $url
     * @param mixed $handler
     * @param array $middlewares
     *
     * @return void
     */
    public static function map(string $method, string $url, $handler, array $middlewares = [])
    {
        self::$routes[$url][$method] = [
            'type' => 'http',
            'handler' => $handler,
            'middlewares' => $middlewares,
            'args' => self::parseArgs($url)
        ];
    }

    /**
     * Describe storage mapping
     *
     * @param string $url
     * @param string $path
     * @return void
     */
    public static function storage(string $url, string $path)
    {
        $args = self::parseArgs($url);
        if (count($args) === 0) {
            $args[] = [
                'name' => 'path',
                'type' => 'string',
                'required' => true
            ];

            $pos = stripos($url, '/');
            if ($pos === false) {
                $url .= '/';
            }

            $url .= '{path}';
        }

        self::$routes[$url][Method::GET] = [
            'type' => 'storage',
            'path' => $path,
            'args' => $args
        ];
    }

    public static function view(string $url, string $view, array $data = [])
    {
        self::$routes[$url][Method::GET] = [
            'type' => 'view',
            'view' => $view,
            'data' => $data
        ];
    }

    /**
     * Describe 404 mapping
     *
     * @param mixed $handler
     * @return void
     */
    public static function fallback(callable $handler)
    {
        self::$routes['*'] = [
            'handler' => $handler
        ];
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }


    private static function parseArgs(string $url): array
    {
        $result = [];
        foreach (explode('/', $url) as $info) {
            $start = stripos($info, '{');
            $end = stripos($info, '}');
            if ($start !== false && $end !== false) {
                $info = substr($info, $start + 1, strlen($info));
                $info = substr($info, 0, $end - 1);

                $info = explode(':', $info);
                $required = true;
                $pos = stripos($info[0], '?');
                $nameCount = strlen($info[0]);
                if ($pos !== false && $pos == $nameCount - 1) {
                    $required = false;
                    $info[0] = substr($info[0], 0, $pos);
                }
                $result[] = [
                    'name' => $info[0],
                    'type' => $info[1] ?? 'string',
                    'required' => $required
                ];
            }
        }
        return $result;
    }
}
