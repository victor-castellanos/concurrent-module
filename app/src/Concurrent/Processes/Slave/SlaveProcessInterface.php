<?php
namespace Concurrent\Processes\Slave;

use Concurrent\Processes\ProcessInterface;

/**
 * Interface SlaveProcessInterface
 * @package Concurrent\Processes\Slave
 */
interface SlaveProcessInterface extends ProcessInterface {

    /**
     * Sends a message to the queue and waits for a response on the
     * same pipe
     *
     * @param $actionHandler
     * @param $actionData
     * @param $timeout
     * @internal param $message
     * @return mixed
     */
    public function sendMessageAndReceive($actionHandler, $actionData, $timeout);

}
