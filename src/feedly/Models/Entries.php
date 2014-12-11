<?php

namespace feedly\Models;

class Entries extends FeedlyModel
{

    public function __construct($token)
    {
        parent::__construct($token);

        $this->setEndpoint('/v3/entries');
    }

    public function get($pk)
    {
        return $this->getClient()
                    ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

}