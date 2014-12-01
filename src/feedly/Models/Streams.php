<?php

namespace feedly\Models;

class Streams extends FeedlyModel{

    public function __construct($token) {
        parent::__construct($token);

        $this->setEndpoint('/v3/streams');
    }

    public function get($ids,$data = "ids") {
        return $this->getClient()->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' .$data.'?streamId='.urlencode($ids));
    }

}
