<?php

namespace feedly\Models;

use feedly\AccessTokenStorage\AccessTokenStorage;
use feedly\HTTPClient;
use feedly\Mode\Mode;
use \Exception;
use \RuntimeException;

abstract class FeedlyModel
{

    private $options;
    private $client;

    /**
     * @var Mode
     */
    private $apiMode;

    public function __construct(Mode $apiMode, AccessTokenStorage $accessTokenStorage)
    {
        $this->apiMode = $apiMode;
        $this->client = new HTTPClient();

        $this->getClient()
             ->setCustomHeader(array(
                                   "Authorization: OAuth " . $accessTokenStorage->getAccessToken(),
                                   'Content-Type: application/json'
                               ));
    }

    public function fetch()
    {
        if (!is_string($this->getEndpoint()))
            throw new RuntimeException('An endpoint must be set');

        return $this->client->get($this->apiMode->getApiBaseUrl() . $this->getEndpoint());
    }

    public function persist()
    {
        if (!is_string($this->getEndpoint()))
            throw new \RuntimeException('An endpoint must be set');

        return $this->client->post($this->apiMode->getApiBaseUrl() . $this->getEndpoint());
    }

    public function setOptions($options)
    {
        $this->options = $options;

        $this->client->setPostParams($this->options);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    abstract public function getEndpoint();
}
