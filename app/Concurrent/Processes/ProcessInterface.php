<?php
namespace Concurrent\Processes;

/**
 * Interface ProcessInterface
 * @package Concurrent\Processes
 */
interface ProcessInterface {

    /**
     * Sends a message to the queue
     *
     * @param $actionHandler
     * @param $actionData
     * @internal param $message
     * @return mixed
     */
    public function sendMessage($actionHandler, $actionData);

}
