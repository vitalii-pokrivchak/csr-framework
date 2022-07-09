<?php

namespace Csr\Framework\Poi;

class Messages
{
    private static string $currentLang = 'en_US';

    private static array $messages = [
        'en_US' => [
            'label' => 'value',
            'number' => '#label must be a number',
            'number.greater' => '#label must be greater than %d',
            'number.less' => '#label must be less than %d',
            'number.min' => '#label must be greater than %d',
            'number.max' => '#label must be less than %d',
            'number.port' => '#label must be a valid port',
            'number.positive' => '#label must be positive',
            'number.negative' => '#label must be negative',
            'number.unsafe.max' => '#label must be less than allowed by system',
            'number.unsafe.min' => '#label must be greater than allowed by system',
            'number.is_integer' => '#label must be integer',
            'number.range' => '#label must be in range from %d to %d',
            'string' => '#label must be a string',
            'string.ip' => '#label must be a valid ip address',
            'string.ipv6' => '#label must be a valid ipv6 address',
            'string.phone' => '#label must be a valid phone number',
            'string.email' => '#label must be a valid email address',
            'string.pattern' => '#label must match a regular expression',
            'string.alphanum' => '#label must only contain alpha-numeric characters',
            'string.credit_card' => '#label must be a valid credit card number',
            'string.guid' => '#label must be a valid guid',
            'string.uuid' => '#label must be a valid uuid',
            'string.mac_address' => '#label must be a valid mac address',
            'string.zip_code' => '#label must be a valid zip code',
            'string.length' => '#label length must be %d characters long',
            'string.min' => '#label length must be greater than or equal to %d characters long',
            'string.max' => '#label length must be less than or equal to %d characters long',
            'array' => '#label must be a array',
            'array.length' => '#label must contains %d items',
            'array.min' => '#label must contains minimum %d items',
            'array.max' => '#label must contains maximum %d items',
            'object' => '#label must be a object'
        ],
        'uk_UA' => [
            'label' => 'значення',
            'number' => '#label має бути числом',
            'number.greater' => '#label має бути більшою ніж %d',
            'number.less' => '#label має бути меншим за %d',
            'number.min' => '#label має бути більшим ніж %d',
            'number.max' => '#label має бути меншим за %d',
            'number.port' => '#label має бути правильним портом',
            'number.positive' => '#label має бути більшим або рівним 0',
            'number.negative' => '#label має бути менше 0',
            'number.unsafe.max' => '#label має бути меншим ніж дозволено системою',
            'number.unsafe.min' => '#label має бути більшим ніж дозволено системою',
            'number.is_integer' => '#label має бути цілим числом',
            'number.range' => '#label має бути в діапазоні від %d до %d',
            'string' => '#label має бути рядком',
            'string.ip' => '#label має бути правильною IP-адресою',
            'string.ipv6' => '#label має бути правильною IPv6-адресою',
            'string.phone' => '#label має бути правильним номером телефону',
            'string.email' => '#label має бути правильною адресою електронної пошти',
            'string.pattern' => '#label має відповідати регулярному виразу',
            'string.alphanum' => '#label має містити лише буквенно-цифрові символи',
            'string.credit_card' => '#label має бути правильним номером банківської картки',
            'string.guid' => '#label має бути правильним guid ідентифікатором',
            'string.uuid' => '#label має бути правильним uuid ідентифікатором',
            'string.mac_address' => '#label має бути правильною mac-адресою',
            'string.zip_code' => '#label має бути правильним поштовим індексом',
            'string.length' => 'довжина #label має складати %d символів',
            'string.min' => 'довжина #label має бути %d або більше символів',
            'string.max' => 'довжина #label має бути %d або менше символів',
            'array' => '#label має бути послідовність з елементів',
            'array.length' => '#label має містити %d елементів',
            'array.min' => '#label має містити мінімум %d елементів',
            'array.max' => '#label має містити максимум %d елементів',
            'object' => '#label має бути об\'єктом'
        ],
    ];

    public static function lang(string $lang)
    {
        self::$currentLang = $lang;
    }

    public static function set(string $lang, array $messages)
    {
        self::$messages[$lang] = $messages;
    }

    public static function get(string $key): ?string
    {
        if (array_key_exists(self::$currentLang, self::$messages)
            && array_key_exists($key, self::$messages[self::$currentLang])) {
            return self::$messages[self::$currentLang][$key];
        }

        if (array_key_exists($key, self::$messages['en_US'])) {
            return self::$messages['en_US'][$key];
        }

        return null;
    }
}