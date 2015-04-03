<?php
namespace Concurrent\Processes\Master;

use Concurrent\Processes\MasterProcessActionNotFoundException;
use Concurrent\Processes\ProcessInterface;

/**
 * Interface MasterProcessInterface
 * @package Concurrent\Processes\Master
 */
interface MasterProcessInterface extends ProcessInterface {

    /**
     * Reads a message for this process
     *
     * @param $timeout
     * @return mixed
     */
    public function receiveMessage($timeout = false);

    /**
     * Executes action handler withing master methods
     * with the parameters specified
     *
     * @param $actionHandler
     * @param $parameters
     * @throws MasterProcessActionNotFoundException
     * @return mixed
     */
    public function executeActionHandler($actionHandler, $parameters);

}
