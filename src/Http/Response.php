<?php

namespace Csr\Framework\Http;

class Response
{
    /**
     * Set status code (200, 404, 400 , 403)
     *
     * @param int $code
     *
     * @return self
     */
    public function status(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    /**
     * Set header
     *
     * @param string $header
     * @param bool $replace
     *
     * @return self
     */
    public function header(string $header, bool $replace = true): self
    {
        header($header, $replace);
        return $this;
    }

    /**
     * Set content type for response (text/html)
     *
     * Use for `$type` parameter **ContentType** constants
     * @param string $type
     *
     * @return self
     */
    public function contentType(string $type): self
    {
        $this->header("Content-Type: $type");
        return $this;
    }

    /**
     * Set cookie
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     *
     * @return void
     */
    public function cookie(string $name, $value, array $options = [])
    {
        setcookie(
            $name,
            $value,
            $options['expire'] ?? 0,
            $options['path'] ?? '',
            $options['domain'] ?? '',
            $options['secure'] ?? false,
            $options['http_only'] ?? false
        );
    }
}
