<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

/**
 * Read this before set your callback url while in Sandbox mode
 * @see https://groups.google.com/forum/#!topic/feedly-cloud/vSo0DuShvDg/discussion
 */
$sandboxMode = true;
$storeAccessTokenToSession = true;

$feedly = new feedly\Feedly($sandboxMode, $storeAccessTokenToSession);

$sandboxMode ?
    $callback = "http://localhost" :
    $callback = "http://" . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['PHP_SELF']);


$model = $feedly->getEndpoint('Profile', $_SESSION['feedly_access_token']);

$response = $model->fetch();

//var_dump($response);
