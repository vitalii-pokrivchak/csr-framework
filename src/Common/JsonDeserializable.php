<?php

namespace Csr\Framework\Common;

interface JsonDeserializable
{
    public function deserialize(array $data);
}