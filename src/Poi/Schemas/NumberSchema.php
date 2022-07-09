<?php

namespace Csr\Framework\Poi\Schemas;

use Csr\Framework\Poi\Messages;

class NumberSchema extends Schema
{
    public function __construct(string $label)
    {
        parent::__construct($label, 'number');
    }

    /**
     * @param int|float $limit
     * @return $this
     */
    public function greater($limit): NumberSchema
    {
        $this->args['greater.limit'] = $limit;
        $this->rules[] = function ($value) {
            if ($value <= $this->args['greater.limit']) {
                $this->errors[] = sprintf(Messages::get('number.greater'), $this->args['greater.limit']);
            }
        };

        return $this;
    }

    /**
     * @param int|float $limit
     * @return $this
     */
    public function less($limit): NumberSchema
    {
        $this->args['less.limit'] = $limit;
        $this->rules[] = function ($value) {
            if ($value >= $this->args['less.limit']) {
                $this->errors[] = sprintf(Messages::get('number.less'), $this->args['less.limit']);
            }
        };

        return $this;
    }

    /**
     * @param int|float $limit
     * @return $this
     */
    public function min($limit): NumberSchema
    {
        $this->args['min.limit'] = $limit;
        $this->rules[] = function ($value) {
            if ($value < $this->args['min.limit']) {
                $this->errors[] = sprintf(Messages::get('number.min'), $this->args['min.limit']);
            }
        };

        return $this;
    }

    /**
     * @param int|float $limit
     * @return $this
     */
    public function max($limit): NumberSchema
    {
        $this->args['max.limit'] = $limit;
        $this->rules[] = function ($value) {
            if ($value > $this->args['max.limit']) {
                $this->errors[] = sprintf(Messages::get('number.max'), $this->args['max.limit']);
            }
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function port(): NumberSchema
    {
        $this->rules[] = function ($value) {
            if ($value < 0 || $value > 65535) {
                $this->errors[] = Messages::get('number.port');
            }
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function positive(): NumberSchema
    {
        $this->rules[] = function ($value) {
            if ($value <= 0) {
                $this->errors[] = Messages::get('number.positive');
            }
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function negative(): NumberSchema
    {
        $this->rules[] = function ($value) {
            if ($value >= 0) {
                $this->errors[] = Messages::get('number.negative');
            }
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function unsafe(): NumberSchema
    {
        $this->rules[] = function ($value) {
            if ($value > PHP_INT_MAX) {
                $this->errors[] = Messages::get('number.unsafe.max');
            }
            if ($value < PHP_INT_MIN) {
                $this->errors[] = Messages::get('number.unsafe.min');
            }
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function integer(): NumberSchema
    {
        $this->rules[] = function ($value) {
            if (!is_integer($value)) {
                $this->errors[] = Messages::get('number.is_integer');
            }
        };
        return $this;
    }

    /**
     * @param int|float $from
     * @param int|float $to
     * @return $this
     */
    public function range($from, $to): NumberSchema
    {
        $this->args['range.from'] = $from;
        $this->args['range.to'] = $to;
        $this->rules[] = function ($value) {
            if ($value < $this->args['range.from'] || $value > $this->args['range.to']) {
                $this->errors[] = sprintf(
                    Messages::get('number.range'),
                    $this->args['range.from'],
                    $this->args['range.to']
                );
            }
        };

        return $this;
    }
}