<?php
use Services\MessageQueues\Beanstalkd\BeanstalkdMessageQueue;

/**
 * @property mixed _queueMock
 * @property mixed _jobMock
 */
abstract class BeanstalkdMessageQueueTestCase extends PHPUnit_Framework_TestCase {
    /**
     * @var $_sut BeanstalkdMessageQueue
     */
    protected $_sut;

    public function setUp() {
        $this->_queueMock = $this->getMockBuilder('\Pheanstalk_PheanstalkInterface')->disableOriginalConstructor()
                            ->getMock();
        $this->_jobMock = $this->getMockBuilder('\Pheanstalk_Job')->disableOriginalConstructor()->getMock();
        $this->_sut = new BeanstalkdMessageQueue($this->_queueMock);
    }
}

/**
 * @property mixed dataTemp
 * @property int tubeTmp
 * @property int jobId
 */
class when_putting_an_element_into_the_queue extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_call_the_interface_put_method() {
        $this->_queueMock->expects($this->once())->method('putInTube')
        ->with($this->tubeTmp = 100, serialize($this->dataTemp = ['data' => 'data']));
        $this->_sut->put($this->tubeTmp, $this->dataTemp);
    }

    /**
     * @test
     */
    public function it_should_return_true_if_the_message_is_placed_correctly() {
        $this->_queueMock->expects($this->once())->method('putInTube')
        ->with($this->tubeTmp = 100, serialize($this->dataTemp = ['data' => 'data']))
        ->will($this->returnValue($this->jobId = 10));
        $this->assertEquals($this->jobId, $this->_sut->put($this->tubeTmp, $this->dataTemp));
    }
}

/**
 * @property int tubeTmp
 * @property mixed dataTemp
 */
class when_putting_an_element_into_the_queue_and_it_fails extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->_queueMock->expects($this->once())->method('putInTube')->will($this->returnValue(false));
    }

    /**
     * @test
     */
    public function it_should_return_false_if_beanstalkd_fails() {
        $this->assertFalse($this->_sut->put(100, ['data' => 'data']));
    }
}

/**
 * @property mixed dataTmp
 */
class when_getting_an_element_from_a_tube_and_it_succeeds extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->_queueMock->expects($this->once())->method('watchOnly')->will($this->returnValue($this->_queueMock));
        $this->_queueMock->expects($this->once())->method('reserve')->will($this->returnValue($this->_jobMock));
        $this->_jobMock->expects($this->once())->method('getData')
        ->will($this->returnValue(serialize($this->dataTmp = ['data' => 'data'])));
        $this->_queueMock->expects($this->once())->method('delete')->with($this->_jobMock);
    }

    /**
     * @test
     */
    public function it_should_return_the_job_from_that_queue_and_delete_it() {
        $this->assertEquals($this->dataTmp, $this->_sut->get(100, null));
    }
}

class when_getting_an_element_from_a_tube_and_it_fails extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->_queueMock->expects($this->once())->method('watchOnly')->will($this->returnValue($this->_queueMock));
        $this->_queueMock->expects($this->once())->method('reserve')->will($this->returnValue(false))->with(100);
        $this->_jobMock->expects($this->never())->method('getData');
    }

    /**
     * @test
     */
    public function it_should_return_false() {
        $this->assertFalse($this->_sut->get(100));
    }
}

class when_getting_an_element_from_a_tube_with_timeout_and_it_fails extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->_queueMock->expects($this->once())->method('watchOnly')->will($this->returnValue($this->_queueMock));
        $this->_queueMock->expects($this->once())->method('reserve')->will($this->returnCallback(function() {sleep(1);}))->with(100, 1);
    }

    /**
     * @test
     */
    public function it_should_throw_the_proper_exception() {
        $this->setExpectedException('\Services\MessageQueues\MessageQueueException');
        $this->_sut->get(100, 1);
    }
}

/**
 * @property mixed dataTmp
 */
class when_peeking_into_a_tube_and_it_succeeds extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->dataTmp = ['data' => 'data'];
        $this->_jobMock->expects($this->once())->method('getData')->will($this->returnValue(serialize($this->dataTmp)));
        $this->_queueMock->expects($this->once())->method('peekReady')->with(100)->will($this->returnValue($this->_jobMock));
    }

    /**
     * @test
     */
    public function it_should_return_the_element_for_inspection() {
        $this->assertEquals($this->dataTmp, $this->_sut->peek(100));
    }
}

class when_peeking_into_a_tube_and_it_fails extends BeanstalkdMessageQueueTestCase {

    public function setUp() {
        parent::setUp();
        $this->_queueMock->expects($this->once())->method('peekReady')->with(100)->will($this->returnValue(false));
    }

    /**
     * @test
     */
    public function it_should_return_false() {
        $this->assertFalse($this->_sut->peek(100));
    }
}
