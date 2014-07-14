<?php
namespace Concurrent\Commons;

/**
 * Interface LoggerInterface
 * @package Concurrent\Commons
 */
interface LoggerInterface {

    /**
     * Single line to log
     *
     * @param $message
     * @return mixed
     */
    public function log($message);

    /**
     * Logs an error as a line with error code
     *
     * @param $message
     * @param int $errorCode
     * @return mixed
     */
    public function error($message, $errorCode = 0x00F);

    /**
     * Logs a success message
     *
     * @param $message
     * @return mixed
     */
    public function success($message);

}
