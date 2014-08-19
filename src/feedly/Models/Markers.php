<?php

namespace feedly\Models;

class Markers extends FeedlyModel{

    public function __construct($token) {
        parent::__construct($token);

        $this->setEndpoint('/v3/markers');
    }

    public function get($pk) {
        return $this->getClient()->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

    public function getUnreadCount() {
        return $this->getClient()->get($this->getApiBaseUrl() . $this->getEndpoint() . '/counts');
    }

    public function markAsRead($body) {
        $this->setOptions($body);
        return $this->getClient()->post($this->getApiBaseUrl() . $this->getEndpoint());
    }

}