<?php

namespace Csr\Framework\Template;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class TwigProvider extends TemplateProvider
{
    protected Twig $twig;

    /**
     * TwigProvider constructor.
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

        $this->twig = new Twig(new FilesystemLoader($path), $cacheEnabled ? ['cache' => $cache] : false);
    }

    /**
     * @param string $view
     * @param array $data
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $view, array $data = [])
    {
        echo $this->twig->render($view, $data);
    }
}
