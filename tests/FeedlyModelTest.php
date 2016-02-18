<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\Models\Profile;
use feedly\Mode\SandBoxMode;
use feedly\AccessTokenStorage\AccessTokenBlackholeStorage;

class FeedlyModelTest extends PHPUnit_Framework_TestCase
{

    public function testShouldInitializeHTTPClient()
    {
        $client = new Profile(new SandBoxMode(), new AccessTokenBlackholeStorage('SOMETOKEN'));

        $this->assertInstanceOf('feedly\HTTPClient', $client->getClient());
    }

    /**
     * @expectedException Exception
     */
    public function testNoTokenFailure()
    {
        $client = new Profile(new SandBoxMode(), new AccessTokenBlackholeStorage('SOMETOKEN'));
        $client->setOptions(array('email' => 'odysseus'));

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
