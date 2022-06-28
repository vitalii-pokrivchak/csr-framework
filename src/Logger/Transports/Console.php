<?php

namespace Csr\Framework\Logger\Transports;

use Csr\Framework\Logger\Level;

class Console extends Transport
{
    public function output(string $message, string $level)
    {
        $stream = fopen('php://stdout', 'w');
        switch ($level) {
            case Level::DEBUG:
                $message = "\033[92m{$message}\033[0m";
                break;
            case Level::INFO:
                $message = "\033[94m{$message}\033[0m";
                break;
            case Level::ERROR:
                $message = "\033[91m{$message}\033[0m";
                break;
            case Level::WARNING:
                $message = "\033[93m${message}\033[0m";
                break;
        }
        $message .= "\n";
        fputs($stream, $message);
        fclose($stream);
    }
}
