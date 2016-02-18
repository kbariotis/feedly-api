<?php

require dirname(__FILE__) . "/../vendor/autoload.php";

/**
 * Read this before set your callback url while in Sandbox mode
 * @see https://groups.google.com/forum/#!topic/feedly-cloud/vSo0DuShvDg/discussion
 */
$feedly = new feedly\Feedly(new feedly\Mode\SandBoxMode(), new feedly\AccessTokenStorage\AccessTokenSessionStorage());

$model = $feedly->profile($_SESSION['feedly_access_token']);

$response = $model->fetch();
