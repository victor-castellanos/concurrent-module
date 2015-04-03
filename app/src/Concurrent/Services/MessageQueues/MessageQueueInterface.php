<?php
namespace Concurrent\Services\MessageQueues;

/**
 * Interface MessageQueueInterface
 * @package Concurrent\Services\MessageQueues
 */
interface MessageQueueInterface {

    /**
     * Puts an element into the queue
     *
     * @param $pipe
     * @param $data
     * @return mixed
     */
    public function put($pipe, $data);

    /**
     * Gets an elements from the queue
     *
     * @param $pipe
     * @param $timeout
     * @return mixed
     */
    public function get($pipe, $timeout);

    /**
     * Peek the next element in the message queue
     *
     * @param $pipe
     * @return mixed
     */
    public function peek($pipe);

}
