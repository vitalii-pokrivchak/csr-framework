<?php

namespace Csr\Framework\Poi\Schemas;

use ReflectionObject;

class ObjectSchema extends Schema
{
    public function __construct(string $label)
    {
        parent::__construct($label, 'object');
    }

    /**
     * @param array|Schema $schema
     * @return $this
     */
    public function properties($schema): ObjectSchema
    {
        $this->args['object.schema'] = $schema;
        $this->rules[] = function ($value) {
            $schema = $this->args['object.schema'];
            if (is_object($value)) {
                $reflection = new ReflectionObject($value);
                foreach ($reflection->getProperties() as $prop) {
                    if (!$prop->isStatic()) {
                        $prop->setAccessible(true);
                        $errors = [];
                        if (!is_array($schema) && $schema instanceof Schema) {
                            $errors = $schema
                                ->label($prop->getName())
                                ->validate($prop->getValue($value));
                        } else {
                            $errors = $schema[$prop->getName()]
                                ->label($prop->getName())
                                ->validate($prop->getValue($value));
                        }
                        array_walk_recursive($errors, function ($error) {
                            $this->errors[] = $error;
                        });
                        $prop->setAccessible(false);
                    }
                }
            }

        };
        return $this;
    }
}