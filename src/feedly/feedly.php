<?php

namespace feedly;

/**
 * PHP Wrapper arround Feedly's REST API.
 *
 * @see    http://developers.feedly.com
 * @author Kostas Bariotis / konmpar@gmail.com / @kbariotis
 *
 */
class Feedly
{
    private
        $_client,
        $_sandboxMode,
        $_apiBaseUrl = "https://cloud.feedly.com",
        $_authorizePath = "/v3/auth/auth",
        $_accessTokenPath = "/v3/auth/token",
        $_storeAccessTokenToSession;

    /**
     * @param boolean $sandbox                   Enable/Disable Sandbox Mode
     * @param boolean $storeAccessTokenToSession Choose whether to store the Access token
     *                                           to $_SESSION or not
     */
    public function __construct($sandbox = false, $storeAccessTokenToSession = true)
    {

        $this->_storeAccessTokenToSession = $storeAccessTokenToSession;
        $this->_sandboxMode               = $sandbox;

        if ($this->_sandboxMode)
            $this->_apiBaseUrl = "https://sandbox.feedly.com";
        if ($this->_storeAccessTokenToSession)
            session_start();
    }

    public function getEndpoint($name, $token)
    {
        $className = __NAMESPACE__ . '\\Models\\' . $name;

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("$name is not valid endpoint for Feedly API");
        }

        $class = new $className($token);
        if ($this->_sandboxMode)
            $class->setApiBaseUrl("https://sandbox.feedly.com");

        return $class;
    }

    /**
     * Return authorization URL
     *
     * @param string $client_id    Client's ID provided by Feedly's Administrators
     * @param string $redirect_uri Endpoint to reroute with the results
     * @param string $response_type
     * @param string $scope
     *
     * @return string Authorization URL
     */
    public function getLoginUrl($client_id, $redirect_uri,
                                $response_type = "code", $scope = "https://cloud.feedly.com/subscriptions")
    {

        return ($this->_apiBaseUrl . $this->_authorizePath . "?" .
            http_build_query(array(
                                 "client_id"     => $client_id,
                                 "redirect_uri"  => $redirect_uri,
                                 "response_type" => $response_type,
                                 "scope"         => $scope
                             )
            )
        );
    }

    /**
     * Exchange a `code` from `getLoginUrl` for `Access` and `Refresh` Tokens
     *
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $client_secret Client's Secret provided by Feedly's Administrators
     * @param string $auth_code     Code obtained from `getLoginUrl`
     * @param string $redirect_url  Endpoint to reroute with the results
     */
    public function getTokens($client_id, $client_secret, $auth_code,
                                   $redirect_url)
    {

        $this->_client = new HTTPClient();

        $this->_client->setCustomHeader(array(
                                            "Authorization: Basic " . base64_encode($client_id . ":" .
                                                                                    $client_secret),
                                        ));

        $this->_client->setPostParams(array(
                                          "code"          => urlencode($auth_code),
                                          "client_id"     => urlencode($client_id),
                                          "client_secret" => urlencode($client_secret),
                                          "redirect_uri"  => $redirect_url,
                                          "grant_type"    => "authorization_code"
                                      )
            , false);

        $response = $this->_client->post($this->_apiBaseUrl . $this->_accessTokenPath);

        $this->storeAccessTokenToSession($response);

        if (isset($response[ 'access_token' ]) &&
            isset($response[ 'refresh_token' ])) {
            return array(
                'access_token' => $response[ 'access_token' ],
                'refresh_token' => $response[ 'refresh_token' ],
                'expires' => $response[ 'expires_in' ],
            );
        }
    }

    public function getRefreshAccessToken($client_id, $client_secret, $refresh_token)
    {

        $this->_client->setCustomHeader(array(
                                            "Authorization: Basic " . base64_encode($client_id . ":" .
                                                                                    $client_secret),
                                        ));

        $this->_client->setPostParams(array(
                                          "refresh_token" => urlencode($refresh_token),
                                          "client_secret" => urlencode($client_secret),
                                          "grant_type"    => $refresh_token
                                      )
            , false);

        $response = $this->_client->post($this->_apiBaseUrl . $this->_accessTokenPath);

        $this->storeAccessTokenToSession($response);

        if (isset($response[ 'access_token' ]))
            return $response[ 'access_token' ];
    }

    private function storeAccessTokenToSession($response)
    {
        if ($this->_storeAccessTokenToSession) {
            if (isset($response[ 'access_token' ])) {
                $_SESSION[ 'feedly_access_token' ] = $response[ 'access_token' ];
                $_SESSION[ 'feedly_access_expires'] = time() + $response[ 'expires_in' ];
                session_write_close();
            }
        }
    }

    /**
     * @return string Access Token from $_SESSION
     */
    private function getAccessTokenFromSession()
    {
        if (isset($_SESSION[ 'feedly_access_token' ]) && isset($_SESSION[ 'feedly_access_expires' ])) {
            if (time() < $_SESSION[ 'feedly_access_expires' ]) {
                return $_SESSION[ 'feedly_access_token' ];
            } else {
                throw new \Exception("Access token expired", 2);        
            }
        } else {
            throw new \Exception("No access token", 1);
        }
    }
}
