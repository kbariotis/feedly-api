<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\Models\FeedlyModel;

class FeedlyModelTest extends PHPUnit_Framework_TestCase
{

    public function testShouldInitializeHTTPClient() {

        $client = new FeedlyModel('SOMETOKEN');

        $this->assertInstanceOf('feedly\HTTPClient', $client->getClient());
    }

    /**
     * @expectedException Exception
     */
    public function testFetchFailureOnEmptyEndpoint() {

        $client = new FeedlyModel('SOMETOKEN');

        $client->fetch();
    }

    /**
     * @expectedException Exception
     */
    public function testPersistFailureOnEmptyEndpoint() {

        $client = new FeedlyModel('SOMETOKEN');

        $client->persist();
    }

    public function testFetch()
    {

        $json = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('FeedlyModel', array('fetch'));

        $feedly->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->fetch());
    }

    public function testPersist()
    {

        $json = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('FeedlyModel', array('persist'));

        $feedly->expects($this->any())
            ->method('persist')
            ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->persist());
    }
}