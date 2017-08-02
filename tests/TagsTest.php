<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\AccessTokenStorage\AccessTokenBlackholeStorage;
use feedly\Mode\SandBoxMode;
use feedly\Models\Tags;

class TagsTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization()
    {
        $model = new Tags(new SandBoxMode(), new AccessTokenBlackholeStorage('SOMETOKEN'));

        $this->assertNotEmpty($model->getEndpoint());
    }

    public function testGetByIds()
    {

        $response = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('Tags', array('get'));

        $feedly->expects($this->any())
            ->method('get')
            ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->get('ids'));
    }

}