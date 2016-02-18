<?php

namespace feedly\AccessTokenStorage;

use feedly\Response\AccessTokenResponse;

class AccessTokenSessionStorage implements AccessTokenStorage
{

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function store(AccessTokenResponse $accessTokenResponse)
    {
        $_SESSION['feedly_access_token']   = $accessTokenResponse->getAccessToken();
        $_SESSION['feedly_access_expires'] = time() + $accessTokenResponse->getExpiresIn();
        session_write_close();
    }

    /**
     * @return string Access Token from $_SESSION
     */
    public function getAccessToken()
    {
        if (isset($_SESSION['feedly_access_token']) && isset($_SESSION['feedly_access_expires'])) {
            if (time() < $_SESSION['feedly_access_expires']) {
                return $_SESSION['feedly_access_token'];
            } else {
                throw new \Exception("Access token expired", 2);
            }
        } else {
            throw new \Exception("No access token", 1);
        }
    }

}
