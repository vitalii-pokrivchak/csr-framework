<?php

namespace Csr\Framework\Http;

use Csr\Framework\Common\Deserializable;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use stdClass;

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
    protected Container $container;

    /**
     * Constructor
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->url = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'] ?? Method::GET;
        $this->host = $_SERVER['SERVER_key'] ?? 'localhost';
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
                    $file['tmp_key'] ?? '',
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
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function headers(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->headers;
        }
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get cookies from request
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function cookies(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get query params from request (?id=1)
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function query(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    /**
     * Get args from request (from dynamic url or from object)
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function args(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->args;
        }
        return $this->args[$key] ?? $default;
    }

    /**
     * Get object from json request
     *
     * @param string $key
     * @param string $type
     * @return object|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function object(string $key = '', string $type = stdClass::class): ?object
    {
        $values = $this->body;

        if ($key !== '') {
            if (array_key_exists($key, $this->body)) {
                $values = $this->body[$key];
            } else {
                return null;
            }
        }

        if ($type === stdClass::class) {
            $obj = new stdClass();
            foreach ($values as $k => $v) {
                $obj->{$k} = $v;
            }
            return $obj;
        } elseif (class_exists($type)) {
            $obj = $this->container->get($type);
            if ($obj instanceof Deserializable) {
                $obj->deserialize($values);
            }
            return $obj;
        } else {
            return null;
        }
    }

    /**
     * Get body from request
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function body(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }

    /**
     * Get files from request
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function files(string $key = '', $default = null)
    {
        if ($key == '') {
            return $this->files;
        }
        return $this->files[$key] ?? $default;
    }

    /**
     * Set argument for request object
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setArg(string $key, $value): void
    {
        $this->args[$key] = $value;
    }
}
