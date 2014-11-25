<?php

namespace feedly\Models;

class Stream extends FeedlyModel{

    public function __construct($token) {
        parent::__construct($token);

        $this->setEndpoint('/v3/streams');
    }

    public function get($pk,$data = "ids") {
        return $this->getClient()->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' .$data.'?streamId='.urlencode($pk));
    }

}