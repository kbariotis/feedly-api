<?php

namespace feedly\Models;

class Profile extends FeedlyModel
{

    public function getEndpoint()
    {
        return '/v3/profile';
    }

}
