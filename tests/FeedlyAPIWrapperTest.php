<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\feedly;

class FeedlyAPIWrapperTest extends PHPUnit_Framework_TestCase
{

    function __construct()
    {
        ini_set("session.use_cookies", 0);

    }

    public function testValidReturnedUrlForAuthorization()
    {

        $feedly = new Feedly(true, false);

        $this->assertNotEmpty($feedly->getLoginUrl("sandbox", "http://localhost"));
    }

    public function testGetTokens()
    {

        $response = array(
            'access_token' => 'dsa5da76d76sa5d67sad567a',
            'expires_in' => '1234',
            'refresh_token' => 'absa5da76d76sa5d87sad597a'
        );

        $feedly = $this->getMock('Feedly', array('getToken'));

        $feedly->expects($this->any())
               ->method('getToken')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->getToken());
    }

    public function testGetRefreshAccessToken()
    {

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
