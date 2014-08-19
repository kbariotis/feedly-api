<?php

namespace feedly;

/**
 * PHP Wrapper arround Feedly's REST API.
 *
 * @see http://developers.feedly.com
 * @author Kostas Bariotis / konmpar@gmail.com / @kbariotis
 *
 */
class Feedly {
    private
        $_client,
        $_access_token,
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

        $this->_client = new HTTPClient();
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

    public function setAccessToken($token) {
        $this->_access_token = $token;

        $this->_client->setCustomHeader(array(
            "Authorization: OAuth " . $this->_access_token,
            'Content-Type: application/json'
        ));
    }

    /**
     * Exchange a `code` got from `getLoginUrl` for an `Access Token`
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $client_secret Client's Secret provided by Feedly's Administrators
     * @param string $auth_code     Code obtained from `getLoginUrl`
     * @param string $redirect_url  Endpoint to reroute with the results
     */
    public function getAccessToken($client_id, $client_secret, $auth_code,
        $redirect_url) {


        $this->_client->setCustomHeader(array (
            "Authorization: Basic " . base64_encode($client_id . ":" .
                $client_secret),
        ));

        $this->_client->setPostParams($post_fields = "code=" . urlencode($auth_code) .
            "&client_id=" . urlencode($client_id) .
            "&client_secret=" . urlencode($client_secret) .
            "&redirect_uri=" . urlencode($redirect_url) .
            "&grant_type=authorization_code");

        $response = '';
        try{
            $response = $this->_client->post($this->_apiBaseUrl . $this->_accessTokenPath);
        }catch(\Exception $e) {}


        if($this->_storeAccessTokenToSession){
            if(isset($response['access_token'])) {
                $_SESSION['access_token'] = $response['access_token'];
                session_write_close();
            }
        }

        if(isset($response['access_token'])) return $response['access_token'];
    }

    public function getRefreshAccessToken($client_id, $client_secret, $refresh_token) {

         $this->_client->setCustomHeader(array (
             "Authorization: Basic " . base64_encode($client_id . ":" .
                 $client_secret),
         ));

         $this->_client->setPostParams("&client_id=" . urlencode($client_id) .
            "&refresh_token=" . urlencode($refresh_token) .
            "&client_secret=" . urlencode($client_secret) .
            "&grant_type=refresh_token");


         $response = '';
         try {
             $response = $this->_client->post($this->_apiBaseUrl . $this->_accessTokenPath);
         } catch (\Exception $e) {
         }

         if ($this->_storeAccessTokenToSession) {
             $_SESSION['access_token'] = $response['access_token'];
             session_write_close();
         }

         if (isset($response['access_token'])) return $response['access_token'];
    }

    /**
     * @see http://developer.feedly.com/v3/profile/#get-the-profile-of-the-user
     * @param string $token Access Token in case we don't store it to $_SESSION
     * @return json Response from the server
     */
    public function getProfile() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/profile');
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
    public function setProfile($email=NULL, $givenName=NULL, $familyName=NULL,
        $picture=NULL, $gender=NULL, $locale=NULL,
        $reader=NULL, $twitter=NULL, $facebook=NULL) {

        //$this->_client->setPostParamsEncType("application/json");
        $this->_client->setPostParams(array(
                'twitter'=>$email
            )
        );

        return $this->_client->post($this->_apiBaseUrl . '/v3/profile');
    }

    /**
     * @see http://developer.feedly.com/v3/preferences/#get-the-preferences-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getPreferences() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/preferences');
    }

    /**
     * @see http://developer.feedly.com/v3/categories/#get-the-list-of-all-categories
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getCategories() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/categories');
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
    public function getSubscriptions() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/subscriptions');
    }

    /**
     * @see http://developer.feedly.com/v3/subscriptions/#subscribe-to-a-feed
     * @opts Array of subscription options as per the documentation
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function setSubscription($opts) {
        $this->_client->setPostParamsEncType("application/json");

        $this->_client->setPostParams($opts);

        return $this->_client->post('/v3/subscriptions');
    }

    /**
     * @see http://developer.feedly.com/v3/feeds/#get-the-metadata-about-a-specific-feed
     * @param  string $feedId Feed's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getFeedMetadata($feedId, $token=NULL) {
        return $this->_client->post('/v3/feeds/' . urlencode($feedId));
    }

    /**
     * @see http://developer.feedly.com/v3/streams/#get-the-content-of-a-stream
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getStreamContent($streamId, $count=NULL, $ranked=NULL,
        $unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $token=NULL) {

        $this->_client->setGetParams(array(
            "streamId"=>$streamId,
            "count"=>$count,
            "ranked"=>$ranked,
            "unreadOnly"=>$unreadOnly,
            "newerThan"=>$newerThan,
            "continuation"=>$continuation
        ));
        return $this->_client->get($this->_apiBaseUrl . '/v3/streams/contents');

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
    public function getTopics() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/topics');
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getTags() {
        return $this->_client->get($this->_apiBaseUrl . '/v3/tags');
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function searchFeeds($queryToSearch, $n=NULL) {
        $this->_client->setGetParams(array(
            "q"=>$queryToSearch,
            "n"=>$n
        ));
        return $this->_client->get($this->_apiBaseUrl . '/v3/search/feeds?');
    }

    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    public function getUnreadCounts($autorefresh=NULL, $newerThan=NULL,
        $streamId=NULL, $token=NULL) {

        $this->_client->setGetParams(array(
            "autorefresh"=>$autorefresh,
            "newerThan"=>$newerThan,
            "streamId"=>$streamId,
        ));

        return $this->_client->get($this->_apiBaseUrl . '/v3/markers/counts?autorefresh=');
    }

    /**
     * @return string Access Token from $_SESSION
     */
    protected function _getAccessTokenFromSession(){
        if(isset($_SESSION['access_token'])){
            return $_SESSION['access_token'];
        }else {
            throw new \Exception("No access token", 1);
        }
    }
}

?>
