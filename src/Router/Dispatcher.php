<?php

namespace Csr\Framework\Router;

use Csr\Framework\Http\Request;
use Csr\Framework\Http\Response;
use Csr\Framework\Http\ContentType;
use Csr\Framework\Http\Controller;
use Csr\Framework\Http\StatusCode;
use Csr\Framework\Template\TemplateProvider;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Invoker\Exception\NotEnoughParametersException;
use Invoker\Invoker;
use JsonSerializable;

class Dispatcher
{
    protected Request $request;
    protected Response $response;
    protected Invoker $invoker;
    protected Container $container;

    public function __construct(Request $request, Response $response, Invoker $invoker, Container $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->invoker = $invoker;
        $this->container = $container;
    }

    /**
     * @param $route
     * @throws DependencyException
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     * @throws NotFoundException
     */
    public function dispatch($route)
    {
        if ($route != null) {
            $type = $route['type'] ?? null;
            switch ($type) {
                case 'http':
                    $this->dispatchHttp($route);
                    break;
                case 'storage':
                    $this->dispatchStorage($route);
                    break;
                case 'view':
                    $this->dispatchView($route);
                    break;
                default:
                    $this->dispatchFallback();
                    break;
            }
        } else {
            $this->dispatchFallback();
        }
        exit;
    }

    /**
     * @param $route
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function dispatchHttp($route)
    {
        $accessGranted = true;
        if (count($route['middlewares']) > 0) {
            foreach ($route['middlewares'] as $middleware) {
                $accessGranted = $this->invoker->call([$middleware, 'execute']);
            }
        }

        if ($accessGranted) {
            if (is_array($route['handler']) && count($route['handler']) == 2) {
                $controllerName = $route['handler'][0];
                $action = $route['handler'][1];

                /** @var Controller $controller */
                $controller = $this->container->get($controllerName);
                $result = $this->invoker->call([$controller, $action]);

                if (!($result instanceof Controller)) {
                    $controller->finish($result);
                }
            } else {
                $result = $this->invoker->call($route['handler']);
                if ($result instanceof JsonSerializable) {
                    $this->response->contentType(ContentType::JSON);
                    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo $result;
                }
            }
        } else {
            $this->dispatchFallback();
        }
    }

    private function dispatchStorage($route)
    {
        $args = $this->request->args();
        if (count($args) > 0) {
            $key = array_key_first($args);
            $filepath = $args[$key];
            if ($filepath !== '') {
                $pos = stripos($route['path'], DIRECTORY_SEPARATOR);
                if ($pos === false) {
                    $route['path'] = $route['path'] . DIRECTORY_SEPARATOR;
                }
                $file = $route['path'] . $filepath;
                if (file_exists($file)) {
                    $this->resolveExtension($file);
                    readfile($file);
                } else {
                    $this->response->status(StatusCode::NOT_FOUND);
                }
            }
        } else {
            $this->response->status(StatusCode::NOT_FOUND);
        }
    }

    /**
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    private function dispatchFallback()
    {
        $fallback = Route::getRoutes()['*'] ?? null;
        $this->response->status(StatusCode::NOT_FOUND);
        if ($fallback != null) {
            $this->invoker->call($fallback['handler']);
        }
    }

    private function dispatchView($route)
    {
        /** @var TemplateProvider $templateProvider */
        $templateProvider = $this->container->get(TemplateProvider::class);

        if ($templateProvider !== null) {
            $view = $route['view'];
            $data = $route['data'] ?? [];
            $templateProvider->render($view, [
                'data' => $data,
                'args' => $this->request->args()
            ]);
        } else {
            $this->dispatchFallback();
        }
    }

    private function resolveExtension($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $aliases = [
            'ico' => 'image/vnd.microsoft.icon',
            'svg' => 'image/svg+xml',
            'json' => ContentType::JSON,
            'html' => ContentType::HTML,
            'css' => ContentType::CSS,
            'xml' => ContentType::XML,
            'gif' => ContentType::GIF,
            'png' => ContentType::PNG,
            'jpg' => ContentType::JPEG,
            'js' => ContentType::JAVASCRIPT,
            'txt' => ContentType::TEXT,
            'mp4' => ContentType::MP4,
            'mp3' => ContentType::MPEG,
            'pdf' => ContentType::PDF,
            'webm' => ContentType::WEBM,
            'webp' => ContentType::WEBP,
            'wav' => ContentType::WAV,
            'gzip' => 'application/x-gzip',
            'rar' => 'application/x-compressed',
            'zip' => 'application/x-compressed',
            'tgz' => 'application/x-compressed',
        ];

        if (array_key_exists($ext, $aliases)) {
            $this->response->contentType($aliases[$ext]);
        } else {
            $this->response->contentType(ContentType::OCTET_STREAM);
        }
    }
}
