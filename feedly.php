<?php

/**
 * PHP Wrapper arround Feedly's REST API.
 *
 * @see http://developers.feedly.com
 * @author Kostas Bariotis / konmpar@gmail.com / @kbariotis
 *
 */
class Feedly {
    private
        $_apiBaseUrl = "https://cloud.feedly.com",
        $_authorizePath = "/v3/auth/auth",
        $_accessTokenPath = "/v3/auth/token",
        $_storeAccessTokenToSession;

    /**
     * @param boolean $sandbox                   Enable/Disable Sandbox Mode
     * @param boolean $storeAccessTokenToSession Choose whether to store the Access token
     *                                           to $_SESSION or not
     */
    public function __construct($sandbox=FALSE, $storeAccessTokenToSession=TRUE) {
        $this->_storeAccessTokenToSession = $storeAccessTokenToSession;
        if($sandbox) $this->_apiBaseUrl = "https://sandbox.feedly.com";
    }

    /**
     * Return authorization URL
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $redirect_uri  Endpoint to reroute with the results
     * @param string $response_type
     * @param string $scope
     *
     * @return string Authorization URL
     */
    public function getLoginUrl ($client_id, $redirect_uri,
        $response_type="code", $scope="https://cloud.feedly.com/subscriptions") {

        return($this->_apiBaseUrl . $this->_authorizePath . "?" .
            http_build_query(array(
                "client_id"=>$client_id,
                "redirect_uri"=>$redirect_uri,
                "response_type"=>$response_type,
                "scope"=>$scope
                )
            )
        );
    }

    /**
     * Exchange a `code` got from `getLoginUrl` for an `Access Token`
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $client_secret Client's Secret provided by Feedly's Administrators
     * @param string $auth_code     Code obtained from `getLoginUrl`
     * @param string $redirect_url  Endpoint to reroute with the results
     */
    public function GetAccessToken($client_id, $client_secret, $auth_code,
        $redirect_url) {

        $r = null;
        if (($r = @curl_init($this->_apiBaseUrl . $this->_accessTokenPath)) == false) {
            header("HTTP/1.1 500", true, 500);
            die("Cannot initialize cUrl session. Is cUrl enabled for your PHP installation?");
        }

        curl_setopt($r, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($r, CURLOPT_ENCODING, 1);

        curl_setopt($r, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($r, CURLOPT_CAINFO, "C:\wamp\bin\apache\Apache2.2.21\cacert.crt");

        // Add client ID and client secret to the headers.
        curl_setopt($r, CURLOPT_HTTPHEADER, array (
            "Authorization: Basic " . base64_encode($client_id . ":" .
                $client_secret),
        ));

        $post_fields = "code=" . urlencode($auth_code) .
            "&client_id=" . urlencode($client_id) .
            "&client_secret=" . urlencode($client_secret) .
            "&redirect_uri=" . urlencode($redirect_url) .
            "&grant_type=authorization_code";

        curl_setopt($r, CURLOPT_POST, true);
        curl_setopt($r, CURLOPT_POSTFIELDS, $post_fields);

        $response = curl_exec($r);
        $http_status = curl_getinfo($r, CURLINFO_HTTP_CODE);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("oops");

        if($this->_storeAccessTokenToSession){
            $tmp = json_decode($response, true);
            if(!isset($_SESSION['access_token'])){
                session_start();
                $_SESSION['access_token'] = $tmp['access_token'];
                session_write_close();
            }
        }

        return $response;
    }

    /**
     * cUrl Initiliazation
     * @param string $url   URL to query
     * @param string $token Access Token in case we don't store it to $_SESSION
     */
    private function InitCurl($url, $token=NULL) {
        $r = null;

        if (($r = @curl_init($url)) == false) {
            header("HTTP/1.1 500", true, 500);
            die("Cannot initialize cUrl session. Is cUrl enabled for your PHP installation?");
        }

        curl_setopt($r, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($r, CURLOPT_ENCODING, 1);

        curl_setopt($r, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($r, CURLOPT_CAINFO, "C:\wamp\bin\apache\Apache2.2.21\cacert.crt");

        $access_token = is_null($token) ? $this->_getAccessTokenFromSession() : $token;
        curl_setopt($r, CURLOPT_HTTPHEADER, array (
            "Authorization: OAuth " . $access_token
        ));

        return($r);
    }

    /**
     * Query a URL with GET using cUrl after initialization
     * @param string $url        URL to query
     * @param string $get_params Parameters to pass to URL as GET params
     * @param string $token      Access Token in case we don't store it to $_SESSION
     */
    public function ExecRequest($url, $get_params=NULL, $token=NULL) {
        $url = $this->_apiBaseUrl . $url;

        if(is_array($get_params))
            $r = $this->InitCurl($url .'?',
                http_build_query($url, $get_params), $token);
        else
            $r = $this->InitCurl($url, $token);

        $response = curl_exec($r);
        if ($response == false) {
            die("curl_exec() failed. Error: " . curl_error($r));
        }

        $http_status = curl_getinfo($r, CURLINFO_HTTP_CODE);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("oops");
        else
            return $response;
    }

    /**
     * @see http://developer.feedly.com/v3/profile/#get-the-profile-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getProfile($token=NULL) {
        return $this->ExecRequest('/v3/profile', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/preferences/#get-the-preferences-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getPreferences($token=NULL) {
        return $this->ExecRequest('/v3/preferences', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/categories/#get-the-list-of-all-categories
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getCategories($token=NULL) {
        return $this->ExecRequest('/v3/categories', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/subscriptions/#get-the-users-subscriptions
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getSubscriptions($token=NULL) {
        return $this->ExecRequest('/v3/subscriptions', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/feeds/#get-the-metadata-about-a-specific-feed
     * @param  string $feedId Feed's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getFeedMetadata($feedId, $token=NULL) {
        return $this->ExecRequest('/v3/feeds/' . urlencode($feedId),
            NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/streams/#get-the-content-of-a-stream
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getStreamContent($streamId, $count=NULL, $ranked=NULL,
        $unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $token=NULL) {

        return $this->ExecRequest('/v3/streams/contents',
            array(
                "streamId"=>$streamId,
                "count"=>$count,
                "ranked"=>$ranked,
                "unreadOnly"=>$unreadOnly,
                "newerThan"=>$newerThan,
                "continuation"=>$continuation
            ), $token
        );

    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getMixes($streamId, $count=NULL, $unreadOnly=NULL,
        $newerThan=NULL, $hours=NULL, $token=NULL) {

        return $this->ExecRequest('/v3/streams/contents',
            array(
                "streamId"=>$streamId,
                "count"=>$count,
                "hours"=>$hours,
                "unreadOnly"=>$unreadOnly,
                "newerThan"=>$newerThan
            ), $token
        );
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getStreamIds($streamId, $count=NULL, $ranked=NULL,
        $unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $token=NULL) {

        return $this->ExecRequest('/v3/streams/contents',
            array(
                "streamId"=>$streamId,
                "count"=>$count,
                "ranked"=>$ranked,
                "unreadOnly"=>$unreadOnly,
                "newerThan"=>$newerThan,
                "continuation"=>$continuation
            ), $token
        );
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getTopics($token=NULL) {
        return $this->ExecRequest('/v3/topics', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getTags($token=NULL) {
        return $this->ExecRequest('/v3/tags', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function searchFeeds($q, $n=NULL, $token=NULL) {
        return $this->ExecRequest('/v3/search/feeds?',
            array(
                "q"=>$q,
                "n"=>$n
            ), $token
        );
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getUnreadCounts($autorefresh=NULL, $newerThan=NULL,
        $streamId=NULL, $token=NULL) {

        return $this->ExecRequest('/v3/markers/counts?autorefresh=',
            array(
                "autorefresh"=>$autorefresh,
                "newerThan"=>$newerThan,
                "streamId"=>$streamId,
            ), $token
        );
    }

    /**
     * @return string Access Token from $_SESSION
     */
    protected function _getAccessTokenFromSession(){
        if(isset($_SESSION['access_token'])){
            return $_SESSION['access_token'];
        }else {
            throw new Exception("No access token", 1);
        }
    }
}

?>
