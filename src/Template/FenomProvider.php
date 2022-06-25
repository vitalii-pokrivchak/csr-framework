<?php

namespace Csr\Framework\Template;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Fenom;

class FenomProvider extends TemplateProvider
{
    protected Fenom $fenom;

    /**
     * FenomProvider constructor.
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        $path = $container->get('template.path');
        $cache = $container->get('template.cache');
        $cacheEnabled = boolval($container->get('template.cacheEnabled'));

        if ($path == null) {
            throw new Exception('Path to views not provided in config');
        }

        if ($cacheEnabled && $cache == null) {
            throw new Exception('Path to cache not provided in config');
        }

        $this->fenom = Fenom::factory($path, $cache, $cacheEnabled ? 0 : Fenom::DISABLE_CACHE);
    }

    public function render(string $view, array $data = [])
    {
        $this->fenom->display($view, $data);
    }
}
