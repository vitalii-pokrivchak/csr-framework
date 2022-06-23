<?php

namespace Csr\Framework\Kernel;

use Csr\Framework\Http\Method;

class Cors
{
    /**
     * @var array
     */
    protected array $options = [
        'origins' => '',
        'headers' => '',
        'credentials' => false,
        'methods' => ''
    ];

    /**
     * @return $this
     */
    public function allowAnyOrigin(): self
    {
        $this->options['origins'] = '*';
        return $this;
    }

    /**
     * @return $this
     */
    public function allowAnyHeader(): self
    {
        $this->options['headers'] = '*';
        return $this;
    }

    /**
     * @return $this
     */
    public function allowAnyMethod(): self
    {
        $this->options['methods'] = '*';
        return $this;
    }

    /**
     * @return $this
     */
    public function allowCredentials(): self
    {
        $this->options['credentials'] = true;
        return $this;
    }

    /**
     * @param string ...$origins
     * @return $this
     */
    public function withOrigins(string ...$origins): self
    {
        $this->options['origins'] = implode(',', $origins);
        return $this;
    }

    /**
     * @param string ...$methods
     * @return $this
     */
    public function withMethods(string ...$methods): self
    {
        $this->options['methods'] = implode(',', $methods);
        return $this;
    }

    /**
     * @param string ...$headers
     * @return $this
     */
    public function withHeaders(string ...$headers): self
    {
        $this->options['headers'] = implode(',', $headers);
        return $this;
    }

    /**
     * @return $this
     */
    public function withDefault(): self
    {
        $this->options['credentials'] = true;
        $this->options['origins'] = '*';
        $this->options['methods'] = 'GET,HEAD,PUT,PATCH,POST,DELETE';
        return $this;
    }

    /**
     * Apply CORS options
     *
     * @return void
     */
    public function apply()
    {
        if ($this->options['origins'] !== '') {
            header("Access-Control-Allow-Origin: {$this->options['origins']}");
        }
        if ($_SERVER['REQUEST_METHOD'] == Method::OPTIONS) {
            if ($this->options['credentials']) {
                header('Access-Control-Allow-Credentials: true');
            }
            if ($this->options['methods'] !== "") {
                header("Access-Control-Allow-Methods: {$this->options['methods']}");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                if ($this->options['headers'] !== '') {
                    header("Access-Control-Allow-Headers: {$this->options['headers']}");
                } else {
                    header("Access-Control-Allow-Headers : {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
            }
            exit(0);
        }
    }
}
