<?php

namespace Csr\Framework\Poi\Schemas;

use Csr\Framework\Poi\Messages;
use Csr\Framework\Poi\RegexPatterns;

class StringSchema extends Schema
{
    public function __construct(string $label)
    {
        parent::__construct($label, 'string');
    }

    public function ip(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::IP_V4, $value)) {
                $this->errors[] = Messages::get('string.ip');
            }
        };

        return $this;
    }

    public function ipv6(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::IP_V6, $value)) {
                $this->errors[] = Messages::get('string.ipv6');
            }
        };

        return $this;
    }

    public function phone(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::PHONE_NUMBER, $value)) {
                $this->errors[] = Messages::get('string.phone');
            }
        };

        return $this;
    }

    public function email(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = Messages::get('string.email');
            }
        };

        return $this;
    }

    public function pattern(string $regex): StringSchema
    {
        $this->args['pattern.regex'] = $regex;
        $this->rules[] = function ($value) {
            if (!preg_match($this->args['pattern.regex'], $value)) {
                $this->errors[] = Messages::get('string.pattern');
            }
        };

        return $this;
    }

    public function alphanum(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::ALPHA_NUMERIC, $value)) {
                $this->errors[] = Messages::get('string.alphanum');
            }
        };

        return $this;
    }

    public function creditCard(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::CREDIT_CARD, $value)) {
                $this->errors[] = Messages::get('string.credit_card');
            }
        };

        return $this;
    }

    public function guid(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::GUID, $value)) {
                $this->errors[] = Messages::get('string.guid');
            }
        };

        return $this;
    }

    public function uuid(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::UUID, $value)) {
                $this->errors[] = Messages::get('string.uuid');
            }
        };

        return $this;
    }

    public function mac(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::MAC_ADDRESS, $value)) {
                $this->errors[] = Messages::get('string.mac_address');
            }
        };

        return $this;
    }

    public function zipCode(): StringSchema
    {
        $this->rules[] = function ($value) {
            if (!preg_match(RegexPatterns::ZIP_CODE, $value)) {
                $this->errors[] = Messages::get('string.zip_code');
            }
        };

        return $this;
    }

    public function length(int $length): StringSchema
    {
        $this->args['length.value'] = $length;
        $this->rules[] = function ($value) {
            if (strlen($value) === $this->args['length.value']) {
                $this->errors[] = sprintf(Messages::get('string.length'), $this->args['length.value']);
            }
        };

        return $this;
    }

    public function min(int $limit): StringSchema
    {
        $this->args['min.limit'] = $limit;
        $this->rules[] = function ($value) {
            if (strlen($value) < $this->args['min.limit']) {
                $this->errors[] = sprintf(
                    Messages::get('string.min'),
                    $this->args['min.limit']
                );
            }
            return null;
        };

        return $this;
    }

    public function max(int $limit): StringSchema
    {
        $this->args['max.limit'] = $limit;
        $this->rules[] = function ($value) {
            if (strlen($value) > $this->args['max.limit']) {
                $this->errors[] = sprintf(
                    Messages::get('string.max'),
                    $this->args['max.limit']
                );
            }
        };

        return $this;
    }
}