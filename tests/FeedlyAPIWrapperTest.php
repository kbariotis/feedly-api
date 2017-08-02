<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\Feedly;
use feedly\Mode\SandBoxMode;
use feedly\AccessTokenStorage\AccessTokenBlackholeStorage;
use feedly\Response\AccessTokenResponse;

class FeedlyAPIWrapperTest extends PHPUnit_Framework_TestCase
{

    function __construct()
    {
        ini_set("session.use_cookies", 0);

    }

    public function testValidReturnedUrlForAuthorization()
    {
        $feedly = new Feedly(new SandBoxMode(), new AccessTokenBlackholeStorage('SOMETOKEN'));

        $this->assertNotEmpty($feedly->getLoginUrl("sandbox", "http://localhost"));
    }

    public function testGetTokens()
    {

        $response = new AccessTokenResponse([
            'access_token' => 'dsa5da76d76sa5d67sad567a',
            'expires_in' => '1234',
            'refresh_token' => 'absa5da76d76sa5d87sad597a'
        ]);

        $feedly = $this->getMock('Feedly', array('getTokens'));

        $feedly->expects($this->any())
               ->method('getTokens')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->getTokens('client_id', 'client_secret', 'authCode', 'redirectUrl'));
    }

    public function testGetRefreshAccessToken()
    {

        $response = new AccessTokenResponse([
            'access_token' => 'dsa5da76d76sa5d67sad567a',
            'expires_in' => '1234'
        ]);

        $feedly = $this->getMock('Feedly', array('getRefreshAccessToken'));

        $feedly->expects($this->any())
               ->method('getRefreshAccessToken')
               ->will($this->returnValue($response));

        $this->assertEquals($response, $feedly->getRefreshAccessToken('client_id', 'secret_id', 'refresh_token'));
    }

}
