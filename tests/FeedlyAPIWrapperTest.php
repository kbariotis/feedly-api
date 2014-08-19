<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

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

}
