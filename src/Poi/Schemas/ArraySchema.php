<?php

namespace Csr\Framework\Poi\Schemas;

use Csr\Framework\Poi\Messages;

class ArraySchema extends Schema
{
    public function __construct(string $label)
    {
        parent::__construct($label, 'array');
    }

    /**
     * @param array|Schema $schema
     * @return ArraySchema
     */
    public function items($schema): ArraySchema
    {
        $this->args['array.schema'] = $schema;
        $this->rules[] = function ($value) {
            $schema = $this->args['array.schema'];
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $errors = [];
                    if (!is_array($schema) && $schema instanceof Schema) {
                        $errors = $schema->label("[$k]")->validate($v);
                    } else {
                        if (array_key_exists($k, $schema)) {
                            $errors = $schema[$k]->label("[$k]")->validate($v);
                        }
                    }
                    array_walk_recursive($errors, function ($error) {
                        $this->errors[] = $error;
                    });
                }
            }
        };

        return $this;
    }

    public function length(int $length): ArraySchema
    {
        $this->args['array.length'] = $length;
        $this->rules[] = function ($value) {
            if (count($value) !== $this->args['array.length']) {
                $this->errors[] = sprintf(Messages::get('array.length'), $this->args['array.length']);
            }
        };

        return $this;
    }

    public function max(int $limit): ArraySchema
    {
        $this->args['array.max.limit'] = $limit;
        $this->rules[] = function ($value) {
            if (count($value) > $this->args['array.max.limit']) {
                $this->errors[] = sprintf(Messages::get('array.max'), $this->args['array.max.limit']);
            }
        };

        return $this;
    }

    public function min(int $limit): ArraySchema
    {
        $this->args['array.min.limit'] = $limit;
        $this->rules[] = function ($value) {
            if (count($value) < $this->args['array.min.limit']) {
                $this->errors[] = sprintf(Messages::get('array.minn'), $this->args['array.min.limit']);
            }
        };

        return $this;
    }
}