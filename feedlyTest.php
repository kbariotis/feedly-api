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

    public function testValidReturnedUrlForAuthorization()
    {
        $this->assertNotEmpty($this->instance->getLoginUrl("sandbox", "http://localhost"));
    }

    public function testGetAccessTokenThrowsExceptionOnFailure()
    {
        try {
            $this->instance->GetAccessToken();
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }

    public function testReturnedAccessTokenAfterAuthorization()
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

        $feedly = $this->getMock('Feedly', array('GetAccessToken'), array(true, false));

        $feedly->expects($this->any())
             ->method('GetAccessToken')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->GetAccessToken("sandbox", "FUFNPXDNP2J0BF7RCEUZ", "", "http://localhost"));
    }


    public function testExecGetRequestWithoutAccessTokenThrowsException(){
        try {
            $this->instance->ExecGetRequest('/v3/profile');
        }catch (Exception $expected) {
            return;
        }

        $this->fail();
    }

    public function testValidResponseOnExecGetRequest(){
        $json = '
        {
          "access_token": 1385150462,
          "stuff": {
            "this": 2,
            "that": 4,
            "other": 1
            }
        } ';

        $feedly = $this->getMock('Feedly', array('ExecGetRequest'), array(true, false));

        $feedly->expects($this->any())
             ->method('ExecGetRequest')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->ExecGetRequest("/dum/url"));
    }

    public function testValidResponseOnExecPostRequest(){
        $json = '
        {
          "access_token": 1385150462,
          "stuff": {
            "this": 2,
            "that": 4,
            "other": 1
            }
        } ';

        $feedly = $this->getMock('Feedly', array('ExecPostRequest'), array(true, false));

        $feedly->expects($this->any())
             ->method('ExecPostRequest')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->ExecPostRequest('/dummy/url', NULL, array(
            'email'=>'odysseus@ithaka.gr',
            'givenName'=>''
        )));
    }
}
