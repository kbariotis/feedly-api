<?php

namespace feedly\Models;

class Subscriptions extends FeedlyModel
{

    public function getEndpoint()
    {
        return '/v3/subscriptions';
    }

}
