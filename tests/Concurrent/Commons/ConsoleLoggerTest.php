<?php

/**
 * @property mixed $processHandlerMock
 *
 * @outputBuffering enabled
 */
abstract class ConsoleLoggerTestCase extends PHPUnit_Framework_TestCase {
    /**
     * @var $sut \Concurrent\Commons\LoggerInterface
     */
    protected $sut;

    public function setUp() {
        $this->processHandlerMock = $this->getMockBuilder('Concurrent\Commons\ProcessHandlerInterface')->disableOriginalConstructor()->getMock();
        $this->processHandlerMock->expects($this->once())->method('getCurrentProcessId')->will($this->returnValue(1778));
        $this->sut = new \Concurrent\Commons\ConsoleLogger($this->processHandlerMock);
    }
}

/**
 * @property string $message
 * @property mixed _result
 */
class when_printing_a_message extends ConsoleLoggerTestCase {

    public function setUp() {
        parent::setUp();
        $this->sut->log($this->message = 'This is my message');
    }

    /**
     * @test
     */
    public function it_should_return_a_M_indicating_is_a_message() {
        $this->expectOutputRegex('#M#');
    }

    /**
     * @test
     */
    public function it_should_return_the_message_withing_what_is_returned() {
        $this->expectOutputRegex('#' . $this->message . '#');
    }
}

/**
 * @property string $message
 * @property int _errorCode
 */
class when_printing_an_error_message extends ConsoleLoggerTestCase {

    public function setUp() {
        parent::setUp();
        $this->sut->error($this->message = 'This is my message', $this->_errorCode = 0X001F);
    }

    /**
     * @test
     */
    public function it_should_return_an_E_withing_the_message() {
        $this->expectOutputRegex('#E#');
    }

    /**
     * @test
     */
    public function it_should_return_the_message_within_it() {
        $this->expectOutputRegex('#' . $this->message . '#');
    }

    /**
     * @test
     */
    public function it_should_return_the_error_code_within_the_message() {
        $this->expectOutputRegex('#' . "$this->_errorCode" . '#');

    }
}

/**
 * @property string _message
 */
class when_print_a_success_message extends ConsoleLoggerTestCase {

    public function setUp() {
        parent::setUp();
        $this->sut->success($this->_message = 'This is my message');
    }

    /**
     * @test
     */
    public function it_should_return_the_message_within_the_result() {
        $this->expectOutputRegex('#' . $this->_message . '#');
    }

    /**
     * @test
     */
    public function it_should_return_a_S_within_the_message() {
        $this->expectOutputRegex('#S#');

    }
}
