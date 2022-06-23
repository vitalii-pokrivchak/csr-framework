<?php

namespace Csr\Framework\Logger;

use \DateTime;
use Csr\Framework\Logger\Transports\Console;
use Csr\Framework\Logger\Transports\File;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use \Exception;

class Logger
{
    protected array $transports = [
        'file' => File::class,
        'console' => Console::class,
    ];

    protected string $transport;
    protected array $directives = [];
    protected string $format;

    protected Container $container;

    /**
     * Logger constructor.
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->transport = $container->get('logger.transport');
        $this->format = $container->get('logger.format');

        $this->directives = [
            '{datetime}' => fn() => (new DateTime())->format('Y-m-d H:i:s'),
            '{date}' => fn() => (new DateTime())->format('Y-m-d'),
            '{time}' => fn() => (new DateTime())->format('H:i:s'),
            '{day}' => fn() => (new DateTime())->format('d'),
            '{month}' => fn() => (new DateTime())->format('m'),
            '{year}' => fn() => (new DateTime())->format('Y'),
            '{hour}' => fn() => (new DateTime())->format('H'),
            '{min}' => fn() => (new DateTime())->format('i'),
            '{sec}' => fn() => (new DateTime())->format('s'),
            '{os}' => fn() => PHP_OS_FAMILY,
            '{message}' => fn($message) => $message,
            '{_level}' => fn(string $level) => strtolower($level),
            '{level}' => fn(string $level) => $level
        ];
    }

    /**
     * Log with info level
     *
     * @param mixed $message
     * @param string $transport
     * @param string $format
     *
     * @return void
     */
    public function info($message, string $transport = '', string $format = '')
    {
        $this->log(Level::INFO, $message, $transport, $format);
    }

    /**
     * Log with warning level
     *
     * @param mixed $message
     * @param string $transport
     * @param string $format
     *
     * @return void
     */
    public function warning($message, string $transport = '', string $format = '')
    {
        $this->log(Level::WARNING, $message, $transport, $format);
    }

    /**
     * Log with error level
     *
     * @param mixed $message
     * @param string $transport
     * @param string $format
     *
     * @return void
     */
    public function error($message, string $transport = '', string $format = '')
    {
        $this->log(Level::ERROR, $message, $transport, $format);
    }

    /**
     * Log with debug level
     *
     * @param mixed $message
     * @param string $transport
     * @param string $format
     *
     * @return void
     */
    public function debug($message, string $transport = '', string $format = '')
    {
        $this->log(Level::DEBUG, $message, $transport, $format);
    }

    /**
     * Add custom transport to logger
     *
     * @param string $name
     * @param object $transport
     *
     * @return void
     */
    public function addTransport(string $name, object $transport)
    {
        $this->transports[$name] = $transport;
    }

    private function parseFormat(string $level, $message, $format)
    {
        $replacements = [];
        foreach ($this->directives as $name => $directive) {
            if ($name == '{message}') {
                if (is_array($message) || is_object($message)) {
                    $message = json_encode($message, JSON_UNESCAPED_SLASHES);
                }
                $replacements[] = call_user_func($directive, $message);
            } elseif ($name == '{_level}' || $name == '{level}') {
                $replacements[] = call_user_func($directive, $level);
            } else {
                $replacements[] = call_user_func($directive);
            }
        }

        return str_replace(array_keys($this->directives), $replacements, $format);
    }

    private function log(string $level, $message, string $transport = '', string $format = '')
    {
        if (is_string($message)) {
            $message = trim($message);
        }

        $format = $format == '' ? $this->format : $format;
        $transport = $transport == '' ? $this->transport : $transport;

        if ($message != '') {
            $message = $this->parseFormat($level, $message, $format);
            $transportObject = $this->resolveTransport($transport);

            if ($transportObject != null) {
                $transportObject->output($message, $level);
            }
        }
    }

    private function resolveTransport(string $key)
    {
        if (array_key_exists($key, $this->transports)) {
            try {
                return $this->container->get($this->transports[$key]);
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }
}
