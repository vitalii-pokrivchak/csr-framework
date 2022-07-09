<?php

namespace Csr\Framework\Poi\Schemas;

use Csr\Framework\Poi\Exceptions\ValidationException;
use Csr\Framework\Poi\Messages;

abstract class Schema
{
    protected string $label = '';
    protected string $type = '';
    protected array $rules = [];
    protected array $args = [];
    protected array $errors = [];

    public function __construct(string $label, string $type)
    {
        $this->label = $label;
        $this->type = $type;
    }

    public function label(string $label): Schema
    {
        $this->label = $label;
        return $this;
    }

    public function validate($value): array
    {
        if ($this->type === 'string' && !is_string($value)) {
            $this->errors[] = Messages::get('string');
        }
        if ($this->type === 'number' && !is_numeric($value)) {
            $this->errors[] = Messages::get('number');
        }
        if ($this->type === 'array' && !is_array($value)) {
            $this->errors[] = Messages::get('array');
        }
        if ($this->type === 'object' && !is_object($value)) {
            $this->errors[] = Messages::get('object');
        }

        /** @var callable $rule */
        foreach ($this->rules as $rule) {
            $rule($value);
        }

        $this->errors = array_filter($this->errors, function ($error) {
            return !is_null($error);
        });

        $this->errors = array_map(function ($error) {
            return str_replace('#label', "\"$this->label\"", $error);
        }, $this->errors);

        return $this->errors;
    }

    /**
     * @param $value
     * @throws ValidationException
     */
    public function validateOrThrow($value)
    {
        $errors = $this->validate($value);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }
}