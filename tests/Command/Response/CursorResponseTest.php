<?php
namespace Disque\Test\Command;

use PHPUnit_Framework_TestCase;
use Disque\Command\Hello;
use Disque\Command\Response\ResponseInterface;
use Disque\Command\Response\CursorResponse;
use Disque\Command\Response\InvalidResponseException;

class CursorResponseTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $r = new CursorResponse();
        $this->assertInstanceOf(ResponseInterface::class, $r);
    }

    public function testInvalidBodyNotArrayString()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: "test"');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody('test');
    }

    public function testInvalidBodyNotArrayNumeric()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: 128');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(128);
    }

    public function testInvalidBodyElementsNotEnough()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: ["10"]');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['10']);
    }

    public function testInvalidBodyElementsTooMany()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: ["10",["queue1"],"test"]');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['10', ['queue1'], 'test']);
    }

    public function testInvalidBodyElements0NotSet()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: {"9":"10","1":["queue1"]}');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody([9=>'10', 1=>['queue1']]);
    }

    public function testInvalidBodyElements1NotSet()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: {"0":"10","2":["queue1"]}');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody([0=>'10', 2=>['queue1']]);
    }

    public function testInvalidBodyElement1NotNumeric()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: ["test",["queue1"]]');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['test', ['queue1']]);
    }

    public function testInvalidBodyElement2NotArray()
    {
        $this->setExpectedException(InvalidResponseException::class, 'Invalid command response. Command Disque\\Command\\Hello got: ["test","queue1"]');
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['test', 'queue1']);
    }

    public function testParseNoQueues()
    {
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['0', []]);
        $result = $r->parse();
        $this->assertSame([
            'finished' => true,
            'nextCursor' => 0,
            'queues' => [
            ]
        ], $result);
    }

    public function testParseOneQueue()
    {
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['0', ['queue1']]);
        $result = $r->parse();
        $this->assertSame([
            'finished' => true,
            'nextCursor' => 0,
            'queues' => [
                'queue1'
            ]
        ], $result);
    }

    public function testParseSeveralQueues()
    {
        $r = new CursorResponse();
        $r->setCommand(new Hello());
        $r->setBody(['1', ['queue1', 'queue2']]);
        $result = $r->parse();
        $this->assertSame([
            'finished' => false,
            'nextCursor' => 1,
            'queues' => [
                'queue1',
                'queue2'
            ]
        ], $result);
    }

}