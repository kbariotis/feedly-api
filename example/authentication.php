<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

/**
 * Read this before set your callback url while in Sandbox mode
 * @see https://groups.google.com/forum/#!topic/feedly-cloud/vSo0DuShvDg/discussion
 */
$feedly = new feedly\Feedly(new feedly\Mode\SandBoxMode(), new feedly\AccessTokenStorage\AccessTokenSessionStorage());

$loginUrl = $feedly->getLoginUrl("sandbox", "http://localhost");

if (isset($_GET['code'])) {

    /**
     * Response will contain the Access Token and Refresh Token
     */
    $tokens = $feedly->getTokens(
        "sandbox",
        "JSSBD6FZT72058P51XEG",
        $_GET['code'],
        "http://localhost/"
    );

    /**
     * You must update Client's Secret(YDRYI5E8OP2JKXYSDW79 ) once in a while
     * @see https://groups.google.com/forum/#!topic/feedly-cloud/a_cGSAzv8bY
     */

    echo $tokens;
}

if (!isset($_SESSION['feedly_access_token'])) {
    /**
     * After redirection replace "localhost" with your domain
     * keeping the Auth Code GET param
     */
    echo "<a href=\"" . $loginUrl . "\">Authenticate using Feedly</a>";
}
