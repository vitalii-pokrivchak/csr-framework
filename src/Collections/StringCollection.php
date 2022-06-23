<?php

namespace Csr\Framework\Collections;

class StringCollection extends Collection
{
    /**
     * @param string ...$values
     */
    public function __construct(string ...$values)
    {
        $values = array_filter($values, function ($item) {
            return is_string($item);
        });
        parent::__construct($values);
    }

    /**
     * @param string ...$items
     *
     * @return self
     */
    public function add(...$items): self
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function last(): ?string
    {
        $key = array_key_last($this->items);
        if (!is_null($key)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function first(): ?string
    {
        $key = array_key_first($this->items);

        if (!is_null($key)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @param int $key
     * @return string|null
     */
    public function value(int $key): ?string
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return null;
    }
}
