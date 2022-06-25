<?php

namespace Csr\Framework\Kernel;

use \Closure;
use Csr\Framework\Logger\Logger;
use Csr\Framework\Router\Router;
use \DI\ContainerBuilder;
use Exception;
use \Invoker\Invoker;
use \Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use \Throwable;

class Kernel
{
    public Builder $builder;
    public Cors $cors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builder = new Builder();
        $this->cors = new Cors();
    }

    /**
     * Build application
     *
     * @param Closure|null $fn
     *
     * @return void
     * @throws Exception
     */
    public function build(?Closure $fn = null)
    {
        if ($fn !== null) {
            $fn->call($this);
        }

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($this->builder->getDefinitions());
        $container = $containerBuilder->build();
        $container->set(Invoker::class, new Invoker(new TypeHintContainerResolver($container), $container));

        $log = $container->get(Logger::class);
        /** @var Router $router */
        $router = $container->get(Router::class);

        if (php_sapi_name() !== 'cli') {
            $this->errorHandler($log);
            $router->init();
        }
    }

    private function errorHandler(Logger $log)
    {
        set_exception_handler(function (Throwable $ex) use ($log) {
            $log->error("{$ex->getMessage()} \n {$ex->getTraceAsString()}");
        });
    }
}
