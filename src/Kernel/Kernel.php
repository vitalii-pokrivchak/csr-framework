<?php

namespace Csr\Framework\Kernel;

use Closure;
use Csr\Framework\Config\Config;
use Csr\Framework\Http\Method;
use Csr\Framework\Http\Request;
use Csr\Framework\Http\Response;
use Csr\Framework\Http\Session;
use Csr\Framework\Logger\Logger;
use Csr\Framework\Router\Router;
use Csr\Framework\Template\TemplateProvider;
use Csr\Framework\Template\TwigProvider;
use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Invoker\Exception\NotEnoughParametersException;
use Invoker\Invoker;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Throwable;
use function DI\get;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class Kernel
{
    protected ?Container $container = null;
    protected ContainerBuilder $containerBuilder;
    protected static ?Kernel $instance = null;

    /**
     * Constructor
     * @param Closure|null $fn
     * @throws DependencyException
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     * @throws NotFoundException
     */
    private function __construct(?Closure $fn = null)
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->addDefinitions([
            'config.path' => '.',
            'logger.path' => './logs/',
            'logger.transport' => 'console',
            'logger.format' => '[{level}] - {time} - {message}',
            'template.cacheEnabled' => false,
            'template.cache' => '',
            TemplateProvider::class => null
        ]);

        if ($fn !== null) {
            $fn->call($this);
        }

        $this->container = $this->containerBuilder->build();
        $this->container->set(Invoker::class , new Invoker(new TypeHintContainerResolver($this->container), $this->container));

        if (php_sapi_name() !== 'cli') {
            $log = $this->container->get(Logger::class);
            /** @var Router $router */
            $router = $this->container->get(Router::class);
            $config = $this->container->get(Config::class);
            $this->errorHandler($log, $config);
            $router->init();
        }
    }

    /**
     * Build application
     *
     * @param Closure|null $fn
     *
     * @return Kernel
     * @throws DependencyException
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     * @throws NotFoundException
     */
    public static function build(?Closure $fn = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($fn);
        }
        return self::$instance;
    }

    private function errorHandler(Logger $log, Config $config)
    {
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());

        set_exception_handler(function (Throwable $ex) use ($log, $whoops, $config) {
            $log->error("{$ex->getMessage()}\n{$ex->getTraceAsString()}");
            $mode = $config->get('APP_MODE', 'dev');
            if ($mode === 'dev' || $mode === 'development') {
                $whoops->handleException($ex);
            }
        });
    }

    /**
     * @param $entry
     * @return mixed|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function get($entry)
    {
        if ($this->container !== null) {
            return $this->container->get($entry);
        }
        return null;
    }

    /**
     * @param string $request
     * @param string $response
     * @return $this
     */
    public function http(string $request, string $response): self
    {
        if (class_exists($request) && class_exists($response)) {
            if (is_subclass_of($request, Request::class) && is_subclass_of($response, Response::class)) {
                $this->containerBuilder->addDefinitions([
                    Request::class => get($request),
                    Response::class => get($response)
                ]);
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @param $target
     * @return $this
     */
    public function instance(string $name, $target): self
    {
        if (is_string($target)) {
            if (class_exists($target)) {
                $this->containerBuilder->addDefinitions([$name => get($target)]);
            }
        }
        else {
            $this->containerBuilder->addDefinitions([$name => $target]);
        }
        return $this;
    }

    /**
     * @param string $path
     * @param string $config
     * @return $this
     */
    public function config(string $path, string $config = Config::class): self
    {
        $this->containerBuilder->addDefinitions(['config.path' => $path]);
        if (class_exists($config) && $config !== Config::class) {
            if (is_subclass_of($config, Config::class)) {
                $this->containerBuilder->addDefinitions([Config::class => get($config)]);
            }
        }
        return $this;
    }

    /**
     * @param string $transport
     * @param string $format
     * @param string $path
     * @param string $log
     * @return $this
     */
    public function logger(
        string $transport = 'console',
        string $format = '[{level}] - {time} - {message}',
        string $path = '',
        string $log = Logger::class): self
    {
        $this->containerBuilder->addDefinitions(['logger.transport' => $transport, 'logger.format' => $format, 'logger.path' => $path]);
        if (class_exists($log) && $log !== Logger::class) {
            if (is_subclass_of($log, Logger::class)) {
                $this->containerBuilder->addDefinitions([Logger::class => get($log)]);
            }
        }
        return $this;
    }

    /**
     * @param string $path
     * @param string $template
     * @param string $cache
     * @param false $cacheEnabled
     * @return $this
     */
    public function template(string $path, string $template = TwigProvider::class , string $cache = '', $cacheEnabled = false): self
    {
        $this->containerBuilder->addDefinitions(['template.path' => $path, 'template.cache' => $cache, 'template.cacheEnabled' => $cacheEnabled]);
        if (class_exists($template)) {
            if (is_subclass_of($template, TemplateProvider::class)) {
                $this->containerBuilder->addDefinitions([TemplateProvider::class => get($template)]);
            }
        }
        return $this;
    }

    /**
     * @param string $router
     * @return $this
     */
    public function router(string $router): self
    {
        if (class_exists($router)) {
            if (is_subclass_of($router, Router::class)) {
                $this->containerBuilder->addDefinitions([Router::class => get($router)]);
            }
        }
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function session($options = []): self
    {
        $this->containerBuilder->addDefinitions([Session::class => Session::start($options)]);
        return $this;
    }

    /**
     * @param array $origins
     * @param array $methods
     * @param array $headers
     * @param bool $credentials
     * @return $this
     */
    public function cors(array $origins = [], array $methods = [], array $headers = [], bool $credentials = false): Kernel
    {
        if (!empty($origins) && !empty($methods) && !empty($headers) && $credentials) {
            $origins = implode(',', $origins);
            $methods = implode(',', $methods);
            $headers = implode(',', $headers);

            header("Access-Control-Allow-Origin: {$origins}");

            if ($_SERVER['REQUEST_METHOD'] == Method::OPTIONS) {
                header('Access-Control-Allow-Credentials: true');
                header("Access-Control-Allow-Methods: {$methods}");
                header("Access-Control-Allow-Headers : {$headers}");
            }
        }
        else {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET,HEAD,PUT,PATCH,POST,DELETE");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers : {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
        }

        return $this;
    }

    public function doctrine(callable $fn): self
    {
        $this->containerBuilder->addDefinitions(['database.source' => $fn]);
        $this->containerBuilder->addDefinitions([EntityManager::class => function (Container $container, Config $config) {
            $dbSource = $container->get('database.source');
            foreach ($dbSource['dbSource'] as $k => $v) {
                $dbSource['dbSource'][$k] = $config->getOrThrow($v);
            }
            $dbSource['dbSource']['driver'] = $dbSource['driver'];
            $ormConfig = ORMSetup::createAnnotationMetadataConfiguration([$dbSource['pathToEntities']], $dbSource['isDevMode']);
            return EntityManager::create($dbSource['dbSource'], $ormConfig);
        }]);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function compile(string $path): self
    {
        $this->containerBuilder->enableCompilation($path);
        return $this;
    }
}
