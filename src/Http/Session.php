<?php

namespace Csr\Framework\Http;

class Session
{
    /**
     * @param array $options
     * @return Session
     */
    public static function start(array $options = []): Session
    {
        session_start($options);
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

    /**
     * @return string
     */
    public function id(): string
    {
        return session_id();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return session_name();
    }

    /**
     * @return bool
     */
    public function abort(): bool
    {
        return session_abort();
    }

    /**
     * @return bool
     */
    public function reset(): bool
    {
        return session_reset();
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return session_status();
    }
}