<?php
use Concurrent\Processes\ProcessMessage;

/**
 * @property int processFrom
 * @property string action
 * @property int processTo
 * @property mixed dataTest
 */
abstract class ProcessMessageTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_sut ProcessMessage
     */
    protected $_sut;

    public function setUp()
    {
        $this->_sut = new ProcessMessage($this->processFrom = 10, $this->processTo = 20, $this->action = 'action', $this->dataTest = ['data' => 'data']);
    }
}

class when_getting_argument_elements extends ProcessMessageTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_return_all_elements_passed()
    {
        $this->assertEquals($this->processFrom, $this->_sut->getProcessFrom());
        $this->assertEquals($this->processTo, $this->_sut->getProcessTo());
        $this->assertEquals($this->dataTest, $this->_sut->getData());
        $this->assertEquals($this->action, $this->_sut->getAction());
    }


}
