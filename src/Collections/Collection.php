<?php

namespace Csr\Framework\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

abstract class Collection implements IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @param mixed ...$items
     * @return $this
     */
    abstract public function add(...$items): self;

    /**
     * @return mixed|null
     */
    abstract public function last();

    /**
     * @return mixed|null
     */
    abstract public function first();

    /**
     * @param int $key
     * @return mixed|null
     */
    abstract public function value(int $key);

    /**
     * Collection constructor.
     * @param $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param callable $fn
     * @return $this
     */
    public function map(callable $fn): self
    {
        foreach ($this->items as &$item) {
            $item = $fn($item);
        }
        return $this;
    }

    /**
     * @param callable $fn
     * @return $this
     */
    public function filter(callable $fn): self
    {
        foreach ($this->items as $index => $item) {
            $result = $fn($item);

            if (is_bool($result) && !$result) {
                unset($this->items[$index]);
            }
        }

        return $this;
    }

    /**
     * @param callable $fn
     * @return self
     */
    public function forEach(callable $fn): self
    {
        foreach ($this->items as $key => $value) {
            $fn($value, $key);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param int $key
     *
     * @return self
     */
    public function remove(int $key): self
    {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }
        return $this;
    }

    /**
     * @param int $count
     *
     * @return self
     */
    public function skip(int $count): self
    {
        $keys = array_keys(array_slice($this->items, $count, $this->count(), true));

        foreach ($keys as $key) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * @param int $count
     *
     * @return self
     */
    public function take(int $count): self
    {
        if ($count < 0) {
            $this->items = array_slice($this->items, $count, $this->count());
        } else {
            $this->items = array_slice($this->items, 0, $count);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
