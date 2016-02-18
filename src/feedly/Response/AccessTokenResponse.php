<?php

namespace feedly\Response;

use \InvalidArgumentException;

class AccessTokenResponse implements Response
{

    private $accessToken;

    private $expiresIn;

    private $refreshToken;

    public function __construct(array $response)
    {
        if (!isset($response['access_token'])) {
            throw new InvalidArgumentException('Missing access token in response');
        }

        $this->accessToken = $response['access_token'];
        $this->expiresIn   = $response['expires_in'];

        if (isset($response['refresh_token'])) {
            $this->refreshToken = $response['refresh_token'];
        }
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}
