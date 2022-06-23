<?php

namespace Csr\Framework\Http;

class Request
{
    protected string $url;
    protected string $method;
    protected string $host;
    protected string $schema;
    protected int $port;
    protected string $contentType;
    protected array $headers = [];
    protected array $cookies = [];
    protected array $query = [];
    protected array $args = [];
    protected array $body = [];
    protected array $files = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->url = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'] ?? Method::GET;
        $this->host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $this->port = $_SERVER['SERVER_PORT'] ?? 80;
        $this->schema = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https' : 'http';
        $this->cookies = $_COOKIE;
        $this->query = $_GET;
        $this->headers = getallheaders() ?? [];
        $this->contentType = explode(';', $this->headers('Content-Type', ContentType::HTML))[0];

        $input = file_get_contents('php://input');
        $input = $input == false ? '' : $input;
        if ($this->contentType == ContentType::JSON) {
            $this->body = json_decode($input, true) ?? [];
        } elseif ($this->contentType == ContentType::FORM) {
            foreach ($_FILES as $name => $file) {
                $this->files[$name] = new File(
                    $file['name'] ?? '',
                    $file['tmp_name'] ?? '',
                    $file['type'] ?? '',
                    $file['size'] ?? 0.0
                );
            }
            parse_str($input, $this->body);
        } else {
            $this->body = $_POST;
        }
    }

    /**
     * Get url from request
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Get method from request (GET, POST....)
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * Get schema (http or https)
     *
     * @return string
     */
    public function schema(): string
    {
        return $this->schema;
    }

    /**
     * Get server port
     *
     * @return int
     */
    public function port(): int
    {
        return $this->port;
    }

    /**
     * Get request headers
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function headers(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->headers;
        }
        return $this->headers[$name] ?? $default;
    }

    /**
     * Get cookies from request
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function cookies(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->cookies;
        }
        return $this->cookies[$name] ?? $default;
    }

    /**
     * Get query params from request (?id=1)
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function query(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->query;
        }
        return $this->query[$name] ?? $default;
    }

    /**
     * Get args from request (from dynamic url or from object)
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function args(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->args;
        }
        return $this->args[$name] ?? $default;
    }

    /**
     * Get body from request
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function body(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->body;
        }
        return $this->body[$name] ?? $default;
    }

    /**
     * Get files from request
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function files(string $name = '', $default = null)
    {
        if ($name == '') {
            return $this->files;
        }
        return $this->files[$name] ?? $default;
    }

    /**
     * Set argument for request object
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function setArg(string $name, $value): void
    {
        $this->args[$name] = $value;
    }
}
