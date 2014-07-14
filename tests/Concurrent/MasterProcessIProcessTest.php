<?php
use Concurrent\Processes\ProcessInterface;
use Concurrent\Processes\ProcessMessage;

/**
 * @property mixed _processHandlerMock
 * @property mixed _loggerMock
 * @property mixed _queueMock
 */
abstract class MasterProcess_IProcess_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_sut ProcessInterface
     */
    protected $_sut;

    public function setUp()
    {
        $this->_queueMock = $this->getMockBuilder('Services\MessageQueues\MessageQueueInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Services\MessageLogging\LoggerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock = $this->getMockBuilder('Concurrent\Commons\ProcessHandlerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock->expects($this->any())->method('getParentProcessId')->will($this->returnValue(-1));
        $this->_processHandlerMock->expects($this->any())->method('getCurrentProcessId')->will($this->returnValue(10));

        $this->_sut = $this->getMock('Concurrent\Processes\Master\MasterProcess', null, [
            $this->_queueMock,
            $this->_processHandlerMock,
            $this->_loggerMock
        ]);
    }
}

class when_sending_a_message_to_another_process extends MasterProcess_IProcess_TestCase
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
        $message = new ProcessMessage(10, 100, null, ['data' => true]);
        $this->_queueMock->expects($this->once())->method('put')->with(10, $message)->will($this->returnValue(true));
        $this->_sut->sendMessage(100, ['data' => true]);
    }
}

/**
 * @property mixed _queueMock
 * @property mixed _loggerMock
 * @property mixed _processHandlerMock
 */
abstract class MasterProcess_IMasterProcess_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_sut IMasterProcess
     */
    protected $_sut;

    public function setUp()
    {
        $this->_queueMock = $this->getMockBuilder('Services\MessageQueues\MessageQueueInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Services\MessageLogging\LoggerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock = $this->getMockBuilder('Concurrent\Commons\ProcessHandlerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock->expects($this->any())->method('getParentProcessId')->will($this->returnValue(-1));
        $this->_processHandlerMock->expects($this->any())->method('getCurrentProcessId')->will($this->returnValue(10));

        $this->_sut = $this->getMock('Concurrent\Processes\Master\MasterProcess', [
            'executeActionHandler',
            'sendMessage'
        ], [
            $this->_queueMock,
            $this->_processHandlerMock,
            $this->_loggerMock
        ]);
    }

}

class when_receiving_a_message extends MasterProcess_IMasterProcess_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_peek_the_message_and_execute_action_if_message_is_for_master()
    {
        $message = new ProcessMessage(100, 10, 'actionHandler', [
            'data'
        ]);
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($message));
        $this->_queueMock->expects($this->once())->method('get')->will($this->returnValue($message));
        $this->_sut->expects($this->once())->method('executeActionHandler')->with('actionHandler', ['data'])
            ->will($this->returnValue(true));
        $this->assertTrue($this->_sut->receiveMessage());
    }

    /**
     * @test
     */
    public function it_should_peek_the_message_and_do_nothing_if_message_is_not_for_master()
    {
        $message = new ProcessMessage(100, 110, 'actionHandler', [
            'data'
        ]);
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($message));
        $this->_queueMock->expects($this->never())->method('get');
        $this->_sut->expects($this->never())->method('executeActionHandler');
        $this->_sut->expects($this->never())->method('sendMessage');
        $this->assertFalse($this->_sut->receiveMessage());
    }

}

class when_receiving_and_returning_message extends MasterProcess_IMasterProcess_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_receive_the_message_successfully_and_send_the_message()
    {
        $message = new ProcessMessage(100, 10, 'actionHandler', [
            'data'
        ]);
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($message));
        $this->_queueMock->expects($this->once())->method('get')->will($this->returnValue($message));
        $this->_sut->expects($this->once())->method('executeActionHandler')->with('actionHandler', ['data']);
        $this->_sut->receiveMessage();

    }

    /**
     * @test
     */
    public function it_should_peek_the_message_and_do_nothing_if_message_is_not_for_master()
    {
        $message = new ProcessMessage(100, 110, 'actionHandler', [
            'data'
        ]);
        $this->_queueMock->expects($this->once())->method('peek')->will($this->returnValue($message));
        $this->_queueMock->expects($this->never())->method('get')->will($this->returnValue($message));
        $this->_sut->expects($this->never())->method('executeActionHandler');
        $this->_sut->expects($this->never())->method('sendMessage');
        $this->assertFalse($this->_sut->receiveMessage());
    }
}

class when_executing_action_handler_and_it_is_found extends MasterProcess_IMasterProcess_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_queueMock = $this->getMockBuilder('Services\MessageQueues\MessageQueueInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Services\MessageLogging\LoggerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock = $this->getMockBuilder('Concurrent\Commons\ProcessHandlerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_processHandlerMock->expects($this->any())->method('getParentProcessId')->will($this->returnValue(-1));
        $this->_processHandlerMock->expects($this->any())->method('getCurrentProcessId')->will($this->returnValue(10));

        $this->_sut = $this->getMock('Concurrent\Processes\Master\MasterProcess', ['actionHandler'], [
            $this->_queueMock,
            $this->_processHandlerMock,
            $this->_loggerMock
        ]);
    }

    /**
     * @test
     */
    public function it_should_return_the_results_of_the_action()
    {
        $this->_sut->expects($this->once())->method('actionHandler')->with('parameter')
            ->will($this->returnValue(['data']));
        $this->assertEquals(['data'], $this->_sut->executeActionHandler('actionHandler', ['parameter']));
    }
}

class when_executing_action_handler_and_action_is_not_found extends MasterProcess_IMasterProcess_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_sut = $this->getMock('Concurrent\Processes\Master\MasterProcess', null, [
            $this->_queueMock,
            $this->_processHandlerMock,
            $this->_loggerMock
        ]);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception()
    {
        $this->setExpectedException('\Concurrent\Processes\MasterProcessActionNotFoundException');
        $this->_sut->executeActionHandler('actionHandler', ['parameter']);
    }
}
