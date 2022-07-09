<?php

namespace Csr\Framework\Common;

interface Deserializable
{
    /**
     * Deserialize object
     *
     * @param array $data
     * @return mixed
     */
    public function deserialize(array $data);
}