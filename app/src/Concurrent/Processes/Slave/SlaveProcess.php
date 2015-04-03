<?php
namespace Concurrent\Processes\Slave;

use Concurrent\Commons\ProcessHandlerInterface;
use Concurrent\Processes\ExpectingMessageTimeoutException;
use Concurrent\Processes\ProcessMessage;
use Pheanstalk\Exception;
use Psr\Log\LoggerInterface;
use Concurrent\Services\MessageQueues\MessageQueueException;
use Concurrent\Services\MessageQueues\MessageQueueInterface;

/**
 * Class SlaveProcess
 * @package Concurrent\Processes\Slave
 */
abstract class SlaveProcess implements SlaveProcessInterface
{

    /**
     * @var MessageQueueInterface
     */
    private $queueService;
    /**
     * @var ProcessHandlerInterface
     */
    protected $processHandler;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(MessageQueueInterface $queueService,
                                ProcessHandlerInterface $processHandler,
                                LoggerInterface $logger)
    {
        $this->queueService = $queueService;
        $this->processHandler = $processHandler;
        $this->logger = $logger;
    }

    public function sendMessage($actionHandler, $actionData)
    {
        $processMessage = new ProcessMessage(
            $this->processHandler->getCurrentProcessId(),
            $this->processHandler->getParentProcessId(),
            $actionHandler,
            $actionData
        );
        return $this->queueService->put($this->processHandler->getParentProcessId(), $processMessage);
    }

    public function sendMessageAndReceive($actionHandler, $actionData, $timeout = null)
    {
        if ($this->sendMessage($actionHandler, $actionData)) {
            try {
                $timeStart = microtime(true);
                $timeoutOver = false;
                $message = null;
                while (!$timeoutOver && !$message) {
                    /* @var $data ProcessMessage */
                    try {
                        if ($data = $this->queueService->peek($this->processHandler->getParentProcessId())) {
                            if ($data->getProcessTo() == $this->processHandler->getCurrentProcessId()) {
                                $message = $this->queueService->get($this->processHandler->getParentProcessId(), $timeout);
                            }
                        }
                        if ($timeout) $timeoutOver = ((microtime(true) - $timeStart) * 1000) >= $timeout;
                    } catch (Exception $e) {
                    }
                }
            } catch (MessageQueueException $e) {
                throw new ExpectingMessageTimeoutException($e->getMessage());
            }
            return $message->getData();
        }
        return false;
    }

    public abstract function run();


}
