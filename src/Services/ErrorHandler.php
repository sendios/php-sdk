<?php

namespace Sendios\Services;

class ErrorHandler
{
    public const MODE_ERROR = 1;
    public const MODE_EXCEPTION = 2;

    private $mode;

    public function __construct($mode = self::MODE_ERROR)
    {
        $this->setErrorMode($mode);
    }

    /**
     * @param \Exception $e
     * @throws \Exception
     */
    public function handle(\Exception $e): void
    {
        if ($this->mode === self::MODE_EXCEPTION) {
            throw $e;
        }

        $template = ':time Sendios: [:type] :message in :file in line :line. Stack trace: :trace';
        $logMessage = strtr($template, array(
            ':time' => date('Y-m-d H:i:s'),
            ':type' => $e->getCode(),
            ':message' => $e->getMessage(),
            ':file' => $e->getFile(),
            ':line' => $e->getLine(),
            ':trace' => $e->getTraceAsString()
        ));
        error_log($logMessage);
    }

    public function setErrorMode($mode): void
    {
        $this->mode = $mode;
    }
}
