<?php

include_once './feedly.php';
include_once './vendor/autoload.php';

class FeedlyAPITest extends PHPUnit_Framework_TestCase
{
    private $instance;

    function __construct(){
        ini_set("session.use_cookies", 0);
        $this->instance = new Feedly(true, false);
    }
    /**
     * Test valid returned URL for authorization
     */
    public function testGetLoginURL()
    {
        $this->assertNotEmpty($this->instance->getLoginUrl("sandbox", "http://localhost"));
    }

    /**
     * Test `GetAccessToken` without providing a valid Code
     * that must be obtained from the Login URL
     */
    public function testGetAccessTokenThrowsExceptionOnFailure()
    {
        try {
            $this->instance->GetAccessToken();
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }

    /**
     * Test `GetAccessToken` without providing a valid Code
     * that must be obtained from the Login URL
     */
    public function testGetAccessToken()
    {

        $json = '
        {
          "access_token": 1385150462,
          "stuff": {
            "this": 2,
            "that": 4,
            "other": 1
            }
        } ';

        // Create a stub for the SomeClass class.
        $feedly = $this->getMock('Feedly', array('GetAccessToken'), array(true, false));

        // Configure the stub.
        $feedly->expects($this->any())
             ->method('GetAccessToken')
             ->will($this->returnValue($json));

        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertEquals($json, $feedly->GetAccessToken("sandbox", "FUFNPXDNP2J0BF7RCEUZ", "", "http://localhost"));
    }

    /**
     * Testing a Request to API without providing an Access Token
     */
    public function testExecGetRequestWithoutAccessTokenThrowsException(){
        try {
            $this->instance->ExecGetRequest('/v3/profile');
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }

    /**
     * Testing a Request to API without providing an Access Token
     */
    public function testExecGetRequest(){
        $json = '
        {
          "access_token": 1385150462,
          "stuff": {
            "this": 2,
            "that": 4,
            "other": 1
            }
        } ';

        // Create a stub for the SomeClass class.
        $feedly = $this->getMock('Feedly', array('ExecGetRequest'), array(true, false));

        // Configure the stub.
        $feedly->expects($this->any())
             ->method('ExecGetRequest')
             ->will($this->returnValue($json));

        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertEquals($json, $feedly->ExecGetRequest("/dum/url"));
    }

    /**
     * Testing a Request to API without providing an Access Token
     */
    public function testExecPostRequest(){
        $json = '
        {
          "access_token": 1385150462,
          "stuff": {
            "this": 2,
            "that": 4,
            "other": 1
            }
        } ';

        // Create a stub for the SomeClass class.
        $feedly = $this->getMock('Feedly', array('ExecPostRequest'), array(true, false));

        // Configure the stub.
        $feedly->expects($this->any())
             ->method('ExecPostRequest')
             ->will($this->returnValue($json));

        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertEquals($json, $feedly->ExecPostRequest('/dummy/url', NULL, array(
            'email'=>'odysseus@ithaka.gr',
            'givenName'=>''
        )));
    }
}