<?php

require_once "../feedly.php";

/**
 * Read this before set your callback url while in Sandbox mode
 * @see https://groups.google.com/forum/#!topic/feedly-cloud/vSo0DuShvDg/discussion
 */
$sandboxMode = true;

$feedly = new Feedly($sandboxMode, false);

$sandboxMode ?
  $callback = "http://localhost" :
  $callback = "http://" . $_SERVER['HTTP_HOST'] .
  dirname($_SERVER['PHP_SELF']);

$loginUrl = $feedly->getLoginUrl("sandbox", "http://localhost");

if(isset($_GET['code']))
{
  /**
   * Response will contain the Access Token
   */
  echo $feedly->GetAccessToken(
    "sandbox",
    "CM786L1D4P3M9VYUPOB8",
    $_GET['code'],
    "http://localhost"
  );

  /**
   * You must update Client's Secret(CM786L1D4P3M9VYUPOB8) once in a while
   * @see https://groups.google.com/forum/#!topic/feedly-cloud/a_cGSAzv8bY
   */
}
else
{
  /**
   * After redirection replace "localhost" with your domain
   * keeping the Auth Code GET param
   */
  echo "<a href=\"$loginUrl\">Authenticate using Feedly</a>";
}
