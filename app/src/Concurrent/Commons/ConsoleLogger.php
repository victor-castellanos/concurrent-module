<?php

namespace Concurrent\Commons;

use Psr\Log\LoggerInterface;

/**
 * Class ConsoleLogger
 *
 * @package Concurrent\Commons
 */
class ConsoleLogger implements LoggerInterface
{

    protected $processHandler;

    /**
     * @param ProcessHandlerInterface $processHandler
     */
    public function __construct(ProcessHandlerInterface $processHandler) {
        $this->processHandler = $processHandler;
    }

    /**
     * @param $string
     * @param $messageType
     */
    private function printMessage($string, $messageType) {
        echo $this->processHandler->getCurrentProcessId() . " {$messageType} " . date('y-m-d H:i:s') . " - $string\n";
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function emergency($message, array $context = array()) {
        $this->printMessage($message, 'M');
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function alert($message, array $context = array()) {
        $this->printMessage($message, 'A');
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function critical($message, array $context = array()) {
        $this->printMessage($message, 'C');
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function warning($message, array $context = array()) {
        $this->printMessage($message, 'W');
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function notice($message, array $context = array()) {
        $this->printMessage($message, 'N');
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function info($message, array $context = array()) {
        $this->printMessage($message, 'I');
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function debug($message, array $context = array()) {
        $this->printMessage($message, 'D');
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array()) {
        $this->printMessage($message, 'L');
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function error($message, array $context = array()) {
        $this->printMessage($message, 'E');
    }
}
