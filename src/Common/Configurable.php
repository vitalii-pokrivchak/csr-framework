<?php

namespace Csr\Framework\Common;

use Csr\Framework\Config\Config;

interface Configurable
{
    public function configure(Config $config);
}