<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

use feedly\HTTPClient;

class FeedlyAPIWrapperTest extends PHPUnit_Framework_TestCase
{


    private $instance;

    function __construct(){
        ini_set("session.use_cookies", 0);
        $this->instance = new feedly\Feedly(true, false);
    }

    public function testValidReturnedUrlForAuthorization()
    {
        $this->assertNotEmpty($this->instance->getLoginUrl("sandbox", "http://localhost"));
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

        $feedly = $this->getMock('Feedly', array('getAccessToken'));

        $feedly->expects($this->any())
             ->method('getAccessToken')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->getAccessToken("sandbox", "FUFNPXDNP2J0BF7RCEUZ", "", "http://localhost"));
    }

    /**
     * @expectedException Exception
     */
    public function testExecGetRequestWithoutAccessTokenThrowsException(){
        $this->instance->getProfile();
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

        $feedly = $this->getMock('Feedly', array('getProfile'));

        $feedly->expects($this->any())
             ->method('getProfile')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->getProfile());
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

        $feedly = $this->getMock('Feedly', array('setProfile'));

        $feedly->expects($this->any())
             ->method('setProfile')
             ->will($this->returnValue($json));

        $this->assertEquals($json, $feedly->setProfile());
    }
}
