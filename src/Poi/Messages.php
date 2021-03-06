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
            'label' => '????????????????',
            'number' => '#label ?????? ???????? ????????????',
            'number.greater' => '#label ?????? ???????? ?????????????? ?????? %d',
            'number.less' => '#label ?????? ???????? ???????????? ???? %d',
            'number.min' => '#label ?????? ???????? ?????????????? ?????? %d',
            'number.max' => '#label ?????? ???????? ???????????? ???? %d',
            'number.port' => '#label ?????? ???????? ???????????????????? ????????????',
            'number.positive' => '#label ?????? ???????? ?????????????? ?????? ???????????? 0',
            'number.negative' => '#label ?????? ???????? ?????????? 0',
            'number.unsafe.max' => '#label ?????? ???????? ???????????? ?????? ?????????????????? ????????????????',
            'number.unsafe.min' => '#label ?????? ???????? ?????????????? ?????? ?????????????????? ????????????????',
            'number.is_integer' => '#label ?????? ???????? ?????????? ????????????',
            'number.range' => '#label ?????? ???????? ?? ?????????????????? ?????? %d ???? %d',
            'string' => '#label ?????? ???????? ????????????',
            'string.ip' => '#label ?????? ???????? ???????????????????? IP-??????????????',
            'string.ipv6' => '#label ?????? ???????? ???????????????????? IPv6-??????????????',
            'string.phone' => '#label ?????? ???????? ???????????????????? ?????????????? ????????????????',
            'string.email' => '#label ?????? ???????? ???????????????????? ?????????????? ?????????????????????? ??????????',
            'string.pattern' => '#label ?????? ?????????????????????? ?????????????????????? ????????????',
            'string.alphanum' => '#label ?????? ?????????????? ???????? ????????????????-?????????????? ??????????????',
            'string.credit_card' => '#label ?????? ???????? ???????????????????? ?????????????? ?????????????????????? ????????????',
            'string.guid' => '#label ?????? ???????? ???????????????????? guid ??????????????????????????????',
            'string.uuid' => '#label ?????? ???????? ???????????????????? uuid ??????????????????????????????',
            'string.mac_address' => '#label ?????? ???????? ???????????????????? mac-??????????????',
            'string.zip_code' => '#label ?????? ???????? ???????????????????? ???????????????? ????????????????',
            'string.length' => '?????????????? #label ?????? ???????????????? %d ????????????????',
            'string.min' => '?????????????? #label ?????? ???????? %d ?????? ???????????? ????????????????',
            'string.max' => '?????????????? #label ?????? ???????? %d ?????? ?????????? ????????????????',
            'array' => '#label ?????? ???????? ?????????????????????????? ?? ??????????????????',
            'array.length' => '#label ?????? ?????????????? %d ??????????????????',
            'array.min' => '#label ?????? ?????????????? ?????????????? %d ??????????????????',
            'array.max' => '#label ?????? ?????????????? ???????????????? %d ??????????????????',
            'object' => '#label ?????? ???????? ????\'??????????'
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