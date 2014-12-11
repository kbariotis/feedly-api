<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\HTTPClient;

class HTTPClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testGetFailure()
    {
        // Create a stub for the SomeClass class.
        $client = new HTTPClient();

        $client->get('nonexistentsite');

    }

    /**
     * @expectedException Exception
     */
    public function testPostFailure()
    {
        // Create a stub for the SomeClass class.
        $client = new HTTPClient();

        $client->post('nonexistentsite');

    }

    /**
     * @expectedException Exception
     */
    public function testDeleteFailure()
    {
        // Create a stub for the SomeClass class.
        $client = new HTTPClient();

        $client->delete('nonexistentsite');

    }

    public function testGetResponse()
    {
        // Create a stub for the SomeClass class.
        $client = $this->getMock('HTTPClient', array('get'));

        // Configure the stub.
        $client->expects($this->once())
               ->method('get')
               ->with($this->equalTo('http://kostasbariotis.com'))
               ->will($this->returnValue(true));

        $this->assertTrue($client->get('http://kostasbariotis.com'));

    }

    public function testPostResponse()
    {
        // Create a stub for the SomeClass class.
        $client = $this->getMock('HTTPClient', array('post'));

        // Configure the stub.
        $client->expects($this->once())
               ->method('post')
               ->with($this->equalTo('http://kostasbariotis.com'))
               ->will($this->returnValue(true));

        $this->assertTrue($client->post('http://kostasbariotis.com'));

    }
}