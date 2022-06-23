<?php

namespace Csr\Framework\Collections;

class ObjectCollection extends Collection
{
    /**
     * @param object ...$values
     */
    public function __construct(...$values)
    {
        $values = array_filter($values, function ($item) {
            return is_object($item);
        });
        parent::__construct($values);
    }

    /**
     * @param mixed ...$items
     * @return Collection
     */
    public function add(...$items): Collection
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
        return $this;
    }

    /**
     * @return object|null
     */
    public function last(): ?object
    {
        $key = array_key_last($this->items);
        if (!is_null($key)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @return object|null
     */
    public function first(): ?object
    {
        $key = array_key_first($this->items);
        if (!is_null($key)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @param int $key
     * @return object|null
     */
    public function value(int $key): ?object
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return null;
    }
}