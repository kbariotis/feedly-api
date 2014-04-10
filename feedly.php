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
        if($this->_storeAccessTokenToSession) session_start();
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
            throw new Exception("Cannot initialize cUrl session.
                Is cUrl enabled for your PHP installation?");
        }

        curl_setopt($r, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($r, CURLOPT_ENCODING, "");

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
        $tmpr = json_decode($response, true);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("Response from API: " . $tmpr['errorMessage']);

        if($this->_storeAccessTokenToSession){
            if(!isset($_SESSION['access_token'])){
                $_SESSION['access_token'] = $tmpr['access_token'];
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
    private function InitCurl($url, $get_params=NULL, $token=NULL) {
        $r = null;

        if(is_array($get_params))
            $url = $url . '?' . http_build_query($get_params);

        if (($r = @curl_init($url)) == false) {
            throw new Exception("Cannot initialize cUrl session.
                Is cUrl enabled for your PHP installation?");
        }

        curl_setopt($r, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($r, CURLOPT_ENCODING, "");

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
    public function ExecGetRequest($url, $get_params=NULL, $token=NULL) {
        $url = $this->_apiBaseUrl . $url;

        $r = $this->InitCurl($url, $get_params, $token);

        $response = curl_exec($r);
        if ($response == false) {
            throw new Exception("Communication with the API failed: " . curl_error($r));
        }

        $http_status = curl_getinfo($r, CURLINFO_HTTP_CODE);
        $tmpr = json_decode($response, true);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("Something went wrong: " . $tmpr['errorMessage']);
        else
            return $response;
    }

    /**
     * Make a POST request
     * @param string $url        URL to query
     * @param string $get_params Parameters to pass to URL as GET params
     * @param string $post_params Parameters to pass to URL as POST params
     * @param string $token      Access Token in case we don't store it to $_SESSION
     */
    public function ExecPostRequest($url, $get_params=NULL, $post_params=NULL, $token=NULL) {
        $url = $this->_apiBaseUrl . $url;

        $r = $this->InitCurl($url, $get_params, $token);

        $post_fields = http_build_query($post_params);

        curl_setopt($r, CURLOPT_POST, true);
        curl_setopt($r, CURLOPT_POSTFIELDS, $post_fields);


        $response = curl_exec($r);
        if ($response == false) {
            throw new Exception("Communication with the API failed: " . curl_error($r));
        }

        $http_status = curl_getinfo($r, CURLINFO_HTTP_CODE);
        $tmpr = json_decode($response, true);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("Something went wrong: " . $tmpr['errorMessage']);
        else
            return $response;
    }
    /**
     * Make a POST request using JSON format data rather than standard form encoding
     * @param string $url        URL to query
     * @param string $get_params Parameters to pass to URL as GET params
     * @param string $post_params Parameters to pass to URL as POST params
     * @param string $token      Access Token in case we don't store it to $_SESSION
     */
    public function ExecPostJSONRequest($url, $get_params=NULL, $post_params=NULL, $token=NULL) {
        $url = $this->_apiBaseUrl . $url;

        $r = $this->InitCurl($url, $get_params, $token);

        $post_fields = json_encode($post_params);

          	curl_setopt($r, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($r, CURLOPT_POSTFIELDS, $post_fields);

        $access_token = is_null($token) ? $this->_getAccessTokenFromSession() : $token;
        curl_setopt($r, CURLOPT_HTTPHEADER, array (
            "Authorization: OAuth " . $access_token,
            'Content-Type: application/json'
        ));

        $response = curl_exec($r);
        if ($response == false) {
            throw new Exception("Communication with the API failed: " . curl_error($r));
        }

        $http_status = curl_getinfo($r, CURLINFO_HTTP_CODE);
        $tmpr = json_decode($response, true);
        curl_close($r);

        if($http_status!==200)
            throw new Exception("Something went wrong: " . $tmpr['errorMessage']);
        else
            return $response;
    }

    /**
     * @see http://developer.feedly.com/v3/profile/#get-the-profile-of-the-user
     * @param string $token Access Token in case we don't store it to $_SESSION
     * @return json Response from the server
     */
    public function getProfile($token=NULL) {
        return $this->ExecGetRequest('/v3/profile', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/profile/#update-the-profile-of-the-user
     * @param string $email
     * @param string $givenName
     * @param string $familyName
     * @param string $picture
     * @param boolean $gender
     * @param string $locale
     * @param string $reader google reader id
     * @param string $twitter twitter handle. example: edwk
     * @param string $facebook facebook id
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json Response from the server
     */
    public function setProfile($token=NULL, $email=NULL, $givenName=NULL, $familyName=NULL,
        $picture=NULL, $gender=NULL, $locale=NULL,
        $reader=NULL, $twitter=NULL, $facebook=NULL) {
        return $this->ExecPostJSONRequest('/v3/profile', NULL, array(
            'email'=>$email,
            'givenName'=>$givenName,
            'familyName'=>$familyName,
            'picture'=>$picture,
            'gender'=>$gender,
            'locale'=>$locale,
            'reader'=>$reader,
            'twitter'=>$twitter,
            'facebook'=>$facebook
        ), $token);
    }

    /**
     * @see http://developer.feedly.com/v3/preferences/#get-the-preferences-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getPreferences($token=NULL) {
        return $this->ExecGetRequest('/v3/preferences', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/categories/#get-the-list-of-all-categories
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getCategories($token=NULL) {
        return $this->ExecGetRequest('/v3/categories', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/categories/#change-the-label-of-an-existing-category
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @param  string $label Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function renameCategory($token=NULL, $label) {
        return $this->ExecPostJSONRequest('/v3/categories', NULL, array('label'=>$label), $token);
    }

    /**
     * @see http://developer.feedly.com/v3/subscriptions/#get-the-users-subscriptions
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getSubscriptions($token=NULL) {
        return $this->ExecGetRequest('/v3/subscriptions', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/subscriptions/#subscribe-to-a-feed
     * @opts Array of subscription options as per the documentation
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function setSubscription($opts, $token=NULL) {
        return $this->ExecPostJSONRequest('/v3/subscriptions', NULL, $opts, $token);
    }



    /**
     * @see http://developer.feedly.com/v3/feeds/#get-the-metadata-about-a-specific-feed
     * @param  string $feedId Feed's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getFeedMetadata($feedId, $token=NULL) {
        return $this->ExecGetRequest('/v3/feeds/' . urlencode($feedId),
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

        return $this->ExecGetRequest('/v3/streams/contents',
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

        return $this->ExecGetRequest('/v3/streams/contents',
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

        return $this->ExecGetRequest('/v3/streams/contents',
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
        return $this->ExecGetRequest('/v3/topics', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getTags($token=NULL) {
        return $this->ExecGetRequest('/v3/tags', NULL, $token);
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function searchFeeds($q, $n=NULL, $token=NULL) {
        return $this->ExecGetRequest('/v3/search/feeds?',
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

        return $this->ExecGetRequest('/v3/markers/counts?autorefresh=',
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
