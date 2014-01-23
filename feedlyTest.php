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
        ini_set("session.use_cookies", 0);

        $feedly = new Feedly(true, false);
        $this->assertNotEmpty($feedly->getLoginUrl("sandbox", "http://localhost"));
    }

    /**
     * Test `GetAccessToken` without providing a valid Code
     * that must be obtained from the Login URL
     */
    public function testGetAccessTokenWithoutCode()
    {
        ini_set("session.use_cookies", 0);

        $feedly = new Feedly(true, false);
        try {
            $feedly->GetAccessToken("sandbox", "FUFNPXDNP2J0BF7RCEUZ", "", "http://localhost");
        }catch (Exception $expected) {
            $this->assertEquals("Response from API: missing code", $expected->getMessage());
            return;
        }

        $this->fail();
    }

    /**
     * Testing a Request to API without providing an Access Token
     */
    public function testExecRequestWithoutAccessToken(){
        ini_set("session.use_cookies", 0);

        $feedly = new Feedly(true, false);
        try {
            $feedly->ExecRequest('/v3/profile');
        }catch (Exception $expected) {
            $this->assertEquals("No access token", $expected->getMessage());
            return;
        }

        $this->fail();
    }
}