<?php
use Concurrent\Processes\ProcessMessage;
use Concurrent\Processes\Slave\SlaveProcess;
use Concurrent\Services\MessageQueues\MessageQueueException;

/**
 * @property mixed _queueMock
 * @property mixed _loggerMock
 * @property mixed _processHandlerMock
 */
abstract class SlaveProcessTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_sut SlaveProcess
     */
    protected $_sut;

    public function setUp()
    {
        $this->_queueMock = $this->getMockBuilder('Concurrent\Services\MessageQueues\MessageQueueInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Concurrent\Services\MessageLogging\LoggerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock = $this->getMockBuilder('Concurrent\Commons\ProcessHandlerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock->expects($this->any())->method('getParentProcessId')->will($this->returnValue(100));
        $this->_processHandlerMock->expects($this->any())->method('getCurrentProcessId')->will($this->returnValue(10));
        $this->_sut = $this->getMockForAbstractClass('Concurrent\Processes\Slave\SlaveProcess', [
            $this->_queueMock,
            $this->_processHandlerMock,
            $this->_loggerMock
        ]);
    }

}

class when_sending_a_message extends SlaveProcessTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_set_the_message_into_the_queue_service()
    {
        $message = new ProcessMessage(10, 100, 'handleAction', ['data' => true]);
        $this->_queueMock->expects($this->once())->method('put')->with(100, $message)->will($this->returnValue(true));
        $this->_sut->sendMessage('handleAction', ['data' => true]);
    }
}

/**
 * @property int _result
 */
class when_sending_a_message_and_waiting_for_a_response extends SlaveProcessTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_set_the_message_into_the_queue_and_wait_for_a_response()
    {
        $message = new ProcessMessage(10, 100, 'handleAction', ['data' => true]);
        $returnedMessage = new ProcessMessage(100, 10, null, ['test' => 'array']);
        $this->_queueMock->expects($this->once())->method('put')->with(100, $message)->will($this->returnValue(true));
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($returnedMessage));
        $this->_queueMock->expects($this->once())->method('get')->with(100)->will($this->returnValue($returnedMessage));
        $this->assertEquals(['test' => 'array'], $this->_sut->sendMessageAndReceive('handleAction', ['data' => true]));
    }
}

/**
 * @property int _result
 */
class when_sending_a_message_and_waiting_for_a_response_and_timeout_runs_out extends SlaveProcessTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception()
    {
        $message = new ProcessMessage(10, 100, 'handleAction', ['data' => true]);
        $returnedMessage = new ProcessMessage(100, 10, null, true);
        $this->_queueMock->expects($this->once())->method('put')->with(100, $message)->will($this->returnValue(true));
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($returnedMessage));
        $this->_queueMock->expects($this->once())->method('get')->with(100)
            ->will($this->returnCallback(function ($value) {
                throw new MessageQueueException("Failed to get value from tube {$value}");
            }));
        $this->setExpectedException('Concurrent\Processes\ExpectingMessageTimeoutException');
        $this->_sut->sendMessageAndReceive('handleAction', ['data' => true], 1);
    }
}

class when_sending_a_message_and_receiving_data extends SlaveProcessTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_return_false_if_the_message_was_not_able_to_be_sent()
    {
        $message = new ProcessMessage(10, 100, 'handleAction', ['data' => true]);
        $this->_queueMock->expects($this->once())->method('put')->with(100, $message)->will($this->returnValue(false));
        $this->assertFalse($this->_sut->sendMessageAndReceive('handleAction', ['data' => true]));
    }
}
