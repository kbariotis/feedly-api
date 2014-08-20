<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\feedly;

class FeedlyAPIWrapperTest extends PHPUnit_Framework_TestCase
{

    function __construct(){
        ini_set("session.use_cookies", 0);

    }

    public function testValidReturnedUrlForAuthorization()
    {

        $feedly = new Feedly(true, false);

        $this->assertNotEmpty($feedly->getLoginUrl("sandbox", "http://localhost"));
    }

    public function testGetAccessToken() {

        $response = array(
            'access_token' => 'dsa5da76d76sa5d67sad567a'
        );

        $feedly = $this->getMock('Feedly', array('getAccessToken'));

        $feedly->expects($this->any())
            ->method('getAccessToken')
            ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->getAccessToken());
    }

    public function testGetRefreshAccessToken() {

        $response = array(
            'access_token' => 'dsa5da76d76sa5d67sad567a'
        );

        $feedly = $this->getMock('Feedly', array('getRefreshAccessToken'));

        $feedly->expects($this->any())
            ->method('getRefreshAccessToken')
            ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->getRefreshAccessToken());
    }

}
