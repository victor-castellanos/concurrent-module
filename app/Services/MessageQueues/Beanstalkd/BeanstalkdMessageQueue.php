<?php
namespace Services\MessageQueues\Beanstalkd;

use Services\MessageQueues\MessageQueueException;
use Services\MessageQueues\MessageQueueInterface;

/**
 * Class BeanstalkdMessageQueue
 * @package Services\MessageQueues\Beanstalkd
 */
class BeanstalkdMessageQueue implements MessageQueueInterface
{

    /**
     * @var null|\Pheanstalk_PheanstalkInterface
     */
    protected $messageQueue;

    public function __construct(MessageQueueInterface $messageQueue)
    {
        $this->messageQueue = $messageQueue;
    }

    /**
     * Puts an element into the queue
     *
     * @param $pipe
     * @param $data
     * @return mixed
     */
    public function put($pipe, $data)
    {
        return $this->messageQueue->putInTube($pipe, serialize($data));
    }

    /**
     * Gets an elements from the queue
     *
     * @param $pipe
     * @param null $timeout
     * @return bool|mixed
     * @throws \Services\MessageQueues\MessageQueueException
     */
    public function get($pipe, $timeout = null)
    {
        $timeStart = microtime(true);
        if ($job = $this->messageQueue->watchOnly($pipe)->reserve($pipe, $timeout)) {
            $result = $job->getData();
            $this->messageQueue->delete($job);
            return unserialize($result);
        }
        if ($timeout && ((microtime(true) - $timeStart) * 100) >= $timeout) {
            throw new MessageQueueException("Timeout reserving pipe {$pipe}");
        }
        return false;
    }

    /**
     * Peek the next element in the message queue
     *
     * @param $pipe
     * @return mixed
     */
    public function peek($pipe)
    {
        if ($job = $this->messageQueue->peekReady($pipe)) {
            return unserialize($job->getData());
        }
        return false;
    }
}
