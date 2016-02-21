<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\Models\Profile;
use feedly\Mode\SandBoxMode;
use feedly\AccessTokenStorage\AccessTokenBlackholeStorage;


class ProfileTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization()
    {
        $model = new Profile(new SandBoxMode(), new AccessTokenBlackholeStorage('SOMETOKEN'));

        $this->assertNotEmpty($model->getEndpoint());
    }

    public function testResponseOnFetch()
    {

        $response = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('Profile', array('fetch'));

        $feedly->expects($this->any())
               ->method('fetch')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->fetch());
    }

    public function testResponseOnPersist()
    {

        $response = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('Profile', array('persist'));

        $feedly->expects($this->any())
               ->method('persist')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->persist());
    }

}
