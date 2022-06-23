<?php

namespace Csr\Framework\Collections;

class NumericCollection extends Collection
{
    /**
     * @param int|float ...$values
     */
    public function __construct(...$values)
    {
        $values = array_filter($values, function ($item) {
            return is_numeric($item);
        });

        parent::__construct($values);
    }

    /**
     * @return int|float
     */
    public function sum()
    {
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item;
        }

        return $result;
    }

    /**
     * @return int|float
     */
    public function avg()
    {
        if ($this->count() > 0) {
            return $this->sum() / $this->count();
        }
        return 0;
    }

    /**
     * @return int|float
     */
    public function min()
    {
        if ($this->count() > 0) {
            $min = $this->items[0];

            foreach ($this->items as $item) {
                if ($item < $min) {
                    $min = $item;
                }
            }
            return $min;
        }

        return 0;
    }

    /**
     * @return int|float
     */
    public function max()
    {
        if ($this->count() > 0) {
            $max = $this->items[$this->count() - 1];

            foreach ($this->items as $item) {
                if ($item >= $max) {
                    $max = $item;
                }
            }
            return $max;
        }

        return 0;
    }

    /**
     * @param int|float ...$items
     *
     * @return self
     */
    public function add(...$items): self
    {
        foreach ($items as $item) {
            if (is_numeric($item)) {
                $this->items[] = $item;
            }
        }

        return $this;
    }

    /**
     * @return int|float|null
     */
    public function last()
    {
        $key = array_key_last($this->items);
        if ($key != null) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @return int|float|null
     */
    public function first()
    {
        $key = array_key_first($this->items);
        if ($key != null) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * @param int $key
     * @return int|float|null
     */
    public function value(int $key)
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return null;
    }
}
