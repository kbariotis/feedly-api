<?php

namespace feedly\AccessTokenStorage;


use feedly\Response\AccessTokenResponse;

class AccessTokenBlackholeStorage implements AccessTokenStorage
{
    private $accessToken;

    public function __construct($accessToken = '')
    {
        if (empty($accessToken)) {
            throw new \Exception('AccessToken cannot be empty');
        }

        $this->accessToken = $accessToken;
    }

    public function store(AccessTokenResponse $accessTokenResponse)
    {
        $this->accessToken = $accessTokenResponse->getAccessToken();
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
