<?php

namespace feedly\Models;

class Profile extends FeedlyModel{

    public function __construct($token) {
        parent::__construct($token);

        $this->setEndpoint('/v3/profile');
    }

}