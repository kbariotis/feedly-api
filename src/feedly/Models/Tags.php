<?php

namespace feedly\Models;


class Tags extends FeedlyModel
{

    public function getEndpoint()
    {
        return '/v3/tags';
    }

    public function get($pk)
    {
        return $this->getClient()
            ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }
}