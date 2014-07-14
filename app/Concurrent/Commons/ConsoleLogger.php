<?php

namespace Concurrent\Commons;

/**
 * Class ConsoleLogger
 * @package Concurrent\Commons
 */
class ConsoleLogger implements LoggerInterface {

    protected $processHandler;

    /**
     * @param ProcessHandlerInterface $processHandler
     */
    public function __construct(ProcessHandlerInterface $processHandler) {
        $this->processHandler = $processHandler;
    }
    /**
     * Single line to log
     *
     * @param $message
     * @return mixed
     */
    public function log($message) {
        $this->printMessage($message, 'M');
    }

    /**
     * Logs an error as a line with error code
     *
     * @param $message
     * @param int $errorCode
     * @return mixed
     */
    public function error($message, $errorCode = 0x00F) {
        $this->printMessage($message . " - Error Code: $errorCode", 'E');
    }

    /**
     * Logs a success message
     *
     * @param $message
     * @return mixed
     */
    public function success($message) {
        $this->printMessage($message, 'S');
    }

    /**
     * @param $string
     * @param $messageType
     */
    private function printMessage($string, $messageType) {
        echo $this->processHandler->getCurrentProcessId() . " {$messageType} " . date('y-m-d H:i:s') . " - $string\n";
    }
}
