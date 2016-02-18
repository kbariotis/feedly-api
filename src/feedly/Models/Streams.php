<?php

namespace feedly\Models;

class Streams extends FeedlyModel
{
    public function getEndpoint()
    {
        return '/v3/streams';
    }

    public function get($ids, $data = "ids")
    {
        return $this->getClient()
                    ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $data . '?streamId=' . urlencode($ids));
    }

}
