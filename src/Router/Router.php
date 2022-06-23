<?php

namespace Csr\Framework\Router;

use Csr\Framework\Http\Request;
use Csr\Framework\Http\Response;
use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Invoker\Exception\NotEnoughParametersException;

class Router
{
    protected Request $request;
    protected Response $response;
    protected Dispatcher $dispatcher;

    public function __construct(Request $request, Response $response, Dispatcher $dispatcher)
    {
        $this->request = $request;
        $this->response = $response;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Start router
     *
     * @return void
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function init()
    {
        $route = $this->match();
        $this->dispatcher->dispatch($route);
    }

    private function match()
    {
        $url = $this->request->url();
        $method = $this->request->method();
        $routes = Route::getRoutes();
        if (count($routes) > 0) {
            if (array_key_exists($url, $routes)) {
                return $routes[$url][$method] ?? null;
            } else {
                foreach ($routes as $routeUrl => $route) {
                    if (array_key_exists($method, $route)) {
                        $route = $route[$method];
                        $isMatch = $this->patternMatches($routeUrl, $url, $matches);
                        if ($isMatch) {
                            $args = $this->matchArgs($matches);
                            return $this->applyArgs($args, $route);
                        }
                    }
                }
            }
        }
        return null;
    }

    private function matchArgs(array $matches): array
    {
        $matches = array_slice($matches, 1);
        return array_map(function ($match, $index) use ($matches) {
            if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                if ($matches[$index + 1][0][1] > -1) {
                    return trim(
                        substr(
                            $match[0][0],
                            0,
                            $matches[$index + 1][0][1] - $match[0][1]
                        ),
                        '/'
                    );
                }
            }

            return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
        }, $matches, array_keys($matches));
    }

    private function applyArgs($args, $route)
    {
        foreach ($route['args'] as $i => $arg) {
            if (array_key_exists($i, $args)) {
                if ($arg['required']) {
                    if ($args[$i] !== "") {
                        $this->request->setArg($arg['name'], $this->cast($arg['type'], $args[$i]));
                        return $route;
                    }
                    return null;
                }
            }
        }
        return $route;
    }

    private function cast(string $type, $value)
    {

        switch ($type) {
            case "s":
            case "string":
                return (string)($value);
            case "i":
            case "int":
                return (int)($value);
            case "b":
            case "bool":
                return (bool)($value);
            case "float":
            case "f":
            case "d":
            case "double":
                return (float)($value);
            default:
                return $value;
        }
    }

    private function patternMatches($pattern, $uri, &$matches): bool
    {
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);
        return boolval(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE));
    }
}
