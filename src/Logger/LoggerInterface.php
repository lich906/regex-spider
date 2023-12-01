<?php

namespace App\Logger;

interface LoggerInterface
{
    public function log(string $msg): void;

    public function clear(): void;
}
