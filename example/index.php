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

$loginUrl = $feedly->getLoginUrl("sandbox", "http://localhost");

if (isset($_GET['code'])) {

    /**
     * Response will contain the Access Token
     */
    $response = $feedly->getAccessToken(
        "sandbox",
        "YDRYI5E8OP2JKXYSDW79",
        $_GET['code'],
        "http://localhost/"
    );

    /**
     * You must update Client's Secret(YDRYI5E8OP2JKXYSDW79 ) once in a while
     * @see https://groups.google.com/forum/#!topic/feedly-cloud/a_cGSAzv8bY
     */
}

if (!isset($_SESSION['access_token'])) {
    /**
     * After redirection replace "localhost" with your domain
     * keeping the Auth Code GET param
     */
    echo "<a href=\"$loginUrl\">Authenticate using Feedly</a>";
}

if (isset($_SESSION['access_token'])) {

    $feedly->setAccessToken($_SESSION['access_token']);

    $profile = $feedly->getSubscriptions();

    var_dump($profile);

}