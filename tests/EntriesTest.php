<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\Models\Entries;

class EntriesTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization()
    {

        $model = new Entries('TOKEN');

        $this->assertNotEmpty($model->getEndpoint());
    }

    public function testGetByPK()
    {

        $response = array(
            'email' => 'john@doe.com'
        );

        $feedly = $this->getMock('Entries', array('get'));

        $feedly->expects($this->any())
               ->method('get')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->get('pk'));
    }

}