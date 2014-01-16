feedly-api
==========

PHP wrapper around [Feedly's REST API](http://developer.feedly.com/)

Documentation
---------
Constructor:
```php
    /**
     * @param boolean $sandbox                   Enable/Disable Sandbox Mode
     * @param boolean $storeAccessTokenToSession Choose whether to store the Access token
     *                                           to $_SESSION or not
     */
     
    $feedly = new Feedly($sandbox=FALSE, $storeAccessTokenToSession=TRUE);
```

```php
    /**
     * Return authorization URL
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $redirect_uri  Endpoint to reroute with the results
     * @param string $response_type
     * @param string $scope
     *
     * @return string Authorization URL
     */
    $feedly->getLoginUrl($client_id, $redirect_uri,
        $response_type="code", $scope="https://cloud.feedly.com/subscriptions") 
```

```php
    /**
     * Exchange a `code` got from `getLoginUrl` for an `Access Token`
     * @param string $client_id     Client's ID provided by Feedly's Administrators
     * @param string $client_secret Client's Secret provided by Feedly's Administrators
     * @param string $auth_code     Code obtained from `getLoginUrl`
     * @param string $redirect_url  Endpoint to reroute with the results
     */
    $feedly->GetAccessToken($client_id, $client_secret, $auth_code,
        $redirect_url) 
```

```php
/**
     * @see http://developer.feedly.com/v3/profile/#get-the-profile-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getProfile($token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/preferences/#get-the-preferences-of-the-user
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getPreferences($token=NULL)
```

```php
    /**
     * @see http://developer.feedly.com/v3/categories/#get-the-list-of-all-categories
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getCategories($token=NULL)
```

```php
    /**
     * @see http://developer.feedly.com/v3/subscriptions/#get-the-users-subscriptions
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getSubscriptions($token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/feeds/#get-the-metadata-about-a-specific-feed
     * @param  string $feedId Feed's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getFeedMetadata($feedId, $token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/streams/#get-the-content-of-a-stream
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getStreamContent($streamId, $count=NULL, $ranked=NULL,
        $unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getMixes($streamId, $count=NULL, $unreadOnly=NULL,
        $newerThan=NULL, $hours=NULL, $token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $streamId Stream's ID
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getStreamIds($streamId, $count=NULL, $ranked=NULL,
        $unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $token=NULL) 
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getTopics($token=NULL)
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
   $feedly->getTags($token=NULL)
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->searchFeeds($q, $n=NULL, $token=NULL)
```

```php
    /**
     * @see http://developer.feedly.com/v3/profile/
     * @param  string $token Access Token in case we don't store it to $_SESSION
     * @return json   Response from the server
     */
    $feedly->getUnreadCounts($autorefresh=NULL, $newerThan=NULL,
        $streamId=NULL, $token=NULL)
```
