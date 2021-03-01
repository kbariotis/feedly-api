<?php


namespace feedly\Models;


class Boards extends FeedlyModel
{
    public function getEndpoint()
    {
        return '/v3/boards';
    }
    
}
