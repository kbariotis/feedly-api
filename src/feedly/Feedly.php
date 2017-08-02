<?php

namespace feedly;

use feedly\AccessTokenStorage\AccessTokenStorage;
use feedly\Mode\Mode;
use feedly\Response\AccessTokenResponse;

/**
 * PHP Wrapper arround Feedly's REST API.
 *
 * @see    http://developers.feedly.com
 * @author Kostas Bariotis / konmpar@gmail.com / @kbariotis
 *
 */
class Feedly
{
    private $client;

    /**
     * @var Mode\Mode
     */
    private $apiMode;

    /**
     * @var AccessTokenStorage
     */
    private $accessTokenStorage;

    private $authorizePath   = "/v3/auth/auth";
    private $accessTokenPath = "/v3/auth/token";

    /**
     * @param Mode $apiMode
     * @param AccessTokenStorage $accessTokenStorage
     */
    public function __construct(Mode $apiMode, AccessTokenStorage $accessTokenStorage)
    {

        $this->accessTokenStorage = $accessTokenStorage;
        $this->apiMode            = $apiMode;
    }

    public function __call($name, $arguments = [])
    {
        $className = __NAMESPACE__ . '\\Models\\' . ucfirst($name);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("$name is not valid endpoint for Feedly API");
        }

        $class = new $className($this->apiMode, $this->accessTokenStorage);

        return $class;
    }

    /**
     * Return authorization URL
     *
     * @param string $clientId Client's ID provided by Feedly's Administrators
     * @param string $redirectUri Endpoint to reroute with the results
     * @param string $responseType
     * @param string $scope
     *
     * @return string Authorization URL
     */
    public function getLoginUrl($clientId, $redirectUri,
                                $responseType = "code", $scope = "https://cloud.feedly.com/subscriptions")
    {
        return ($this->apiMode->getApiBaseUrl() . $this->authorizePath . "?" .
                http_build_query(array(
                        "client_id" => $clientId,
                        "redirect_uri" => $redirectUri,
                        "response_type" => $responseType,
                        "scope" => $scope
                    )
                )
        );
    }

    /**
     * Exchange a `code` from `getLoginUrl` for `Access` and `Refresh` Tokens
     *
     * @param string $clientId Client's ID provided by Feedly's Administrators
     * @param string $clientSecret Client's Secret provided by Feedly's Administrators
     * @param string $authCode Code obtained from `getLoginUrl`
     * @param string $redirectUrl Endpoint to reroute with the results
     *
     * @return AccessTokenResponse
     */
    public function getTokens($clientId, $clientSecret, $authCode, $redirectUrl)
    {
        $this->client = new HTTPClient();

        $this->client->setCustomHeader(array(
            "Authorization: Basic " . base64_encode($clientId . ":" .
                                                    $clientSecret),
        ));

        $this->client->setPostParams(array(
                "code" => urlencode($authCode),
                "client_id" => urlencode($clientId),
                "client_secret" => urlencode($clientSecret),
                "redirect_uri" => $redirectUrl,
                "grant_type" => "authorization_code"
            )
            , false);

        $response = new AccessTokenResponse(
            $this->client->post($this->apiMode->getApiBaseUrl() . $this->accessTokenPath)
        );

        $this->accessTokenStorage->store($response);

        return $response;
    }

    public function getRefreshAccessToken($client_id, $client_secret, $refresh_token)
    {
        $this->client = new HTTPClient();

        $this->client->setCustomHeader(array(
            "Authorization: Basic " . base64_encode($client_id . ":" .
                                                    $client_secret),
        ));

        $this->client->setPostParams(array(
                "refresh_token" => urlencode($refresh_token),
                "client_id" => urlencode($client_id),
                "client_secret" => urlencode($client_secret),
                "grant_type" => "refresh_token"
            )
            , false);

        $response = new AccessTokenResponse(
            $this->client->post($this->apiMode->getApiBaseUrl() . $this->accessTokenPath)
        );

        $this->accessTokenStorage->store($response);

        return $response;
    }
}
