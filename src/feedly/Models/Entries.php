<?php

namespace feedly\Models;

use feedly\AccessTokenStorage\AccessTokenStorage;

class Entries extends FeedlyModel
{

    public function getEndpoint()
    {
        return '/v3/entries';
    }

    public function get($pk)
    {
        return $this->getClient()
                    ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

}
