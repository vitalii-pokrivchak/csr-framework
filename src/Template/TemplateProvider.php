<?php

namespace Csr\Framework\Template;

abstract class TemplateProvider
{
    /**
     * Render view
     *
     * @param string $view
     * @param array $data
     *
     * @return void
     */
    abstract public function render(string $view, array $data = []);
}
