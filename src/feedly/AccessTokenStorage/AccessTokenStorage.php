<?php

namespace feedly\AccessTokenStorage;


use feedly\Response\AccessTokenResponse;

interface AccessTokenStorage
{

    public function store(AccessTokenResponse $accessTokenResponse);

    public function getAccessToken();
}