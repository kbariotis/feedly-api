<?php

include_once './feedly.php';
include_once './vendor/autoload.php';

class FeedlyAPITest extends PHPUnit_Framework_TestCase
{
    /**
     * Test valid returned URL for authorization
     */
    public function testGetLoginURL()
    {
        $feedly = new Feedly();
        $this->assertNotEmpty($feedly->getLoginUrl("sandbox", "http://localhost"));
    }

    /**
     * Test `GetAccessToken` without providing a valid Code
     * that must be obtained from the Login URL
     */
    public function testGetAccessTokenWithoutCode()
    {
        $feedly = new Feedly();
        try {
            $feedly->GetAccessToken("sandbox", "FUFNPXDNP2J0BF7RCEUZ", "", "http://localhost");
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }

    /**
     * Testing a Request to API without providing an Access Token
     */
    public function testExecRequestWithoutAccessToken(){
        $feedly = new Feedly(true);
        try {
            $feedly->ExecRequest('/v3/profile');
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }
}