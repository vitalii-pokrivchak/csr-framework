<?php

namespace Csr\Framework\Logger\Transports;

class Console extends Transport
{
    public function output(string $message, string $level)
    {
        $message .= "\n";
        $stream = fopen('php://stdout', 'w');
        fputs($stream, $message);
        fclose($stream);
    }
}
