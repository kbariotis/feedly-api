<?php

namespace feedly\Models;

use feedly\HTTPClient;
use SebastianBergmann\Exporter\Exception;
use Symfony\Component\Yaml\Exception\RuntimeException;

class FeedlyModel
{

    private
        $_options,
        $_endpoint,
        $_apiBaseUrl = "https://cloud.feedly.com",
        $_client;

    public function __construct($token)
    {

        if (get_class($this) == 'FeedlyModel')
            throw new Exception('Direct call of this class is not permitted');

        $this->_client = new HTTPClient();

        $this->getClient()
             ->setCustomHeader(array(
                                   "Authorization: OAuth " . $token,
                                   'Content-Type: application/json'
                               ));

    }

    public function fetch()
    {
        if (empty($this->_endpoint))
            throw new RuntimeException('An endpoint must be set');

        return $this->_client->get($this->_apiBaseUrl . $this->_endpoint);
    }

    public function persist()
    {
        if (empty($this->_endpoint))
            throw new \RuntimeException('An endpoint must be set');

        return $this->_client->post($this->_apiBaseUrl . $this->_endpoint);
    }

    public function setOptions($options)
    {
        $this->_options = $options;

        $this->_client->setPostParams($this->_options);

        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setApiBaseUrl($apiBaseUrl)
    {
        $this->_apiBaseUrl = $apiBaseUrl;
    }

    public function getApiBaseUrl()
    {
        return $this->_apiBaseUrl;
    }

    public function setClient($client)
    {
        $this->_client = $client;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setEndpoint($path)
    {
        $this->_endpoint = $path;
    }

    public function getEndpoint()
    {
        return $this->_endpoint;
    }

}