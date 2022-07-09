<?php


namespace Csr\Framework\Adapters\Doctrine;


class DoctrineAdapter
{
    protected bool $isDev = true;
    protected array $dbSource = [];
    protected string $driver;

    public function dev(bool $isDev): self
    {
        $this->isDev = $isDev;
        return $this;
    }

    public function host(string $key): self
    {
        $this->dbSource['host'] = $key;
        return $this;
    }

    public function name(string $key): self
    {
        $this->dbSource['dbname'] = $key;
        return $this;
    }

    public function port(string $key): self
    {
        $this->dbSource['port'] = $key;
        return $this;
    }

    public function user(string $key): self
    {
        $this->dbSource['user'] = $key;
        return $this;
    }

    public function password(string $key): self
    {
        $this->dbSource['password'] = $key;
        return $this;
    }

    public function path(string $key): self
    {
        $this->dbSource['path'] = $key;
        return $this;
    }

    public function driver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function build(): array
    {
        return [
            'dbSource' => $this->dbSource,
            'driver' => $this->driver,
            'isDevMode' => $this->isDev
        ];
    }
}