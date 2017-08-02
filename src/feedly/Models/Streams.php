<?php

namespace feedly\Models;

class Streams extends FeedlyModel
{
    public function getEndpoint()
    {
        return '/v3/streams';
    }

    public function get($ids, $data = "ids", $input = [])
    {
        $query = http_build_query($input + ['streamId' => $ids]);
        return $this->getClient()
            ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $data . '?' . $query);
    }

}
