<?php

namespace Csr\Framework\Http;

/**
 * This class represents a file from request
 *
 * @author vitalii-pokrivchak
 */
class File
{
    protected string $name;
    protected string $path;
    protected string $type;
    protected float $size;

    public function __construct(string $name, string $path, string $type, float $size)
    {
        $this->name = $name;
        $this->path = $path;
        $this->type = $type;
        $this->size = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function save(string $path, string $filename): bool
    {
        return move_uploaded_file($this->path, ($path . DIRECTORY_SEPARATOR . $filename));
    }
}
