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
            array(
                'customizable' => true,
                'created' => 1614603035754,
                'enterprise' => false,
                'label' => 'test',
                'showNotes' => true,
                'showHighlights' => true,
                'isPublic' => false,
                'id' => 'user/aaaa/tag/bbbb',
                'description' => '',
            )
        );

        $feedly = $this->getMock('Boards', array('fetch'));

        $feedly->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->fetch());
    }

}