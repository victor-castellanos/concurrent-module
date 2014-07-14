<?php
namespace Concurrent\Commons;

/**
 * Class ProcessHandler
 * @package Concurrent\Commons
 * @codeCoverageIgnore
 */
class ProcessHandler implements ProcessHandlerInterface {

    /**
     * Gets the parent process ID
     *
     * @return mixed
     */
    public function getParentProcessId() {
        return posix_getppid();
    }

    /**
     * Gets the current user ID
     *
     * @return mixed
     */
    public function getCurrentProcessId() {
        return posix_getpid();
    }
}
