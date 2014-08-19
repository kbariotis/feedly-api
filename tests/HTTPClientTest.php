<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\HTTPClient;

class HTTPClientTest extends PHPUnit_Framework_TestCase
{

    public function testGetMethod() {
        // Create a stub for the SomeClass class.
        $client = $this->getMock('HTTPClient', array('get'));

        // Configure the stub.
        $client->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://kostasbariotis.com'))
            ->will($this->returnValue(TRUE));

        $this->assertTrue($client->get('http://kostasbariotis.com'));

    }

    public function testPostMethod() {
        // Create a stub for the SomeClass class.
        $client = $this->getMock('HTTPClient', array('post'));

        // Configure the stub.
        $client->expects($this->once())
            ->method('post')
            ->with($this->equalTo('http://kostasbariotis.com'))
            ->will($this->returnValue(TRUE));

        $this->assertTrue($client->post('http://kostasbariotis.com'));

    }
}