<?php
namespace Concurrent\Processes\Master;

use Concurrent\Commons\ProcessHandlerInterface;
use Concurrent\Processes\MasterProcessActionNotFoundException;
use Concurrent\Processes\ProcessMessage;
use Pheanstalk\Exception;
use Psr\Log\LoggerInterface;
use Concurrent\Services\MessageQueues\MessageQueueInterface;

/**
 * Class MasterProcess
 *
 * @package Concurrent\Processes\Master
 */
class MasterProcess implements MasterProcessInterface
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

    public function __construct(MessageQueueInterface $messageQueue,
                                ProcessHandlerInterface $processHandler,
                                LoggerInterface $logger) {
        $this->queueService = $messageQueue;
        $this->processHandler = $processHandler;
        $this->logger = $logger;
    }

    /**
     * Reads a message for this process
     *
     * @param $timeout
     *
     * @return mixed
     */
    public function receiveMessage($timeout = null) {
        /**
         * @var ProcessMessage $processMessage
         */
        try {
            if($processMessage = $this->queueService->peek(
                $this->processHandler->getCurrentProcessId(), $timeout)
            ) {
                if($processMessage->getProcessTo() ==
                    $this->processHandler->getCurrentProcessId()) {
                    $processMessage = $this->queueService->get(
                        $this->processHandler->getCurrentProcessId(), $timeout);
                    if($result = $this->executeActionHandler(
                        $processMessage->getAction(), $processMessage->getData())
                    ) {
                        $this->sendMessage($processMessage->getProcessFrom(), $result);
                        return $result;
                    }
                    return false;
                }
            }
        } catch(Exception $e) {
        }
        return false;
    }

    /**
     * Sends a message to the queue
     *
     * @param $messageTo
     * @param $actionData
     *
     * @internal param $actionHandler
     * @internal param $message
     * @return mixed
     */
    public function sendMessage($messageTo, $actionData) {
        $processMessage = new ProcessMessage(
            $this->processHandler->getCurrentProcessId(),
            $messageTo,
            null,
            $actionData
        );
        return $this->queueService->put(
            $this->processHandler->getCurrentProcessId(), $processMessage
        );
    }

    /**
     * Executes action handler withing master methods
     * with the parameters specified
     *
     * @param $actionHandler
     * @param $parameters
     *
     * @throws MasterProcessActionNotFoundException
     * @return mixed
     */
    public function executeActionHandler($actionHandler, $parameters) {
        $parameters = is_array($parameters) ? $parameters : [$parameters];
        if(method_exists($this, $actionHandler)) {
            return call_user_func_array([$this, $actionHandler], $parameters);
        }
        throw new MasterProcessActionNotFoundException(
            "No action {$actionHandler} found on " . __CLASS__
        );
    }
}
