<?php

namespace Csr\Framework\Common;

interface Serializable
{
    /**
     * Serialize object
     *
     * @return array
     */
    public function serialize(): array;
}