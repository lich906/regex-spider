<?php

namespace App\Logger;

class FileLogger implements LoggerInterface
{
    private string $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function log(string $msg): void
    {
        $logStr = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $msg);
        file_put_contents($this->filePath, $logStr, FILE_APPEND);
    }
}
