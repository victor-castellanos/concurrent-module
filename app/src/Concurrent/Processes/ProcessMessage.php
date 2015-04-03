<?php
namespace Concurrent\Processes;

/**
 * Class ProcessMessage
 * @package Concurrent\Processes
 */
class ProcessMessage {

    protected $processFrom;
    protected $processTo;
    protected $action;
    protected $data;

    /**
     * @param $processFrom
     * @param $processTo
     * @param $action
     * @param $data
     */
    public function __construct($processFrom, $processTo, $action, $data) {
        $this->setAction($action);
        $this->setData($data);
        $this->setProcessFrom($processFrom);
        $this->setProcessTo($processTo);
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }
    /**
     * @param mixed $message
     */
    public function setAction($message) {
        $this->action = $message;
    }

    /**
     * @return mixed
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param mixed $processFrom
     */
    public function setProcessFrom($processFrom) {
        $this->processFrom = $processFrom;
    }

    /**
     * @return mixed
     */
    public function getProcessFrom() {
        return $this->processFrom;
    }

    /**
     * @param mixed $processTo
     */
    public function setProcessTo($processTo) {
        $this->processTo = $processTo;
    }

    /**
     * @return mixed
     */
    public function getProcessTo() {
        return $this->processTo;
    }

}
