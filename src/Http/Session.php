<?php

namespace Csr\Framework\Http;

class Session
{
    /**
     * @return Session
     */
    public static function start(): Session
    {
        session_start();
        return new self;
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get(string $key = '', $default = null)
    {
        if ($key === '') {
            return $_SESSION;
        }

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }
}