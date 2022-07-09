<?php

namespace Csr\Framework\Poi;

use Csr\Framework\Poi\Schemas\ArraySchema;
use Csr\Framework\Poi\Schemas\NumberSchema;
use Csr\Framework\Poi\Schemas\ObjectSchema;
use Csr\Framework\Poi\Schemas\StringSchema;

class Poi
{
    public static function configure(array $options)
    {
        Messages::lang($options['lang'] ?? 'en_US');
    }

    public static function number(): NumberSchema
    {
        return new NumberSchema(Messages::get('label'));
    }

    public static function string(): StringSchema
    {
        return new StringSchema(Messages::get('label'));
    }

    public static function object(): ObjectSchema
    {
        return new ObjectSchema(Messages::get('label'));
    }

    public static function array(): ArraySchema
    {
        return new ArraySchema(Messages::get('label'));
    }
}