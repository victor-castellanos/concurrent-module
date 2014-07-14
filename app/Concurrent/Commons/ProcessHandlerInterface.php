<?php
namespace Concurrent\Commons;

/**
 * Interface ProcessHandlerInterface
 * @package Concurrent\Commons
 */
interface ProcessHandlerInterface {

    /**
     * Gets the parent process ID
     *
     * @return mixed
     */
    public function getParentProcessId();

    /**
     * Gets the current user ID
     *
     * @return mixed
     */
    public function getCurrentProcessId();

}
