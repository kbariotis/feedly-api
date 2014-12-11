<?php

namespace feedly\Models;

class Categories extends FeedlyModel
{

    public function __construct($token)
    {
        parent::__construct($token);

        $this->setEndpoint('/v3/categories');
    }

    public function delete($pk)
    {
        return $this->getClient()
                    ->delete($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

    public function changeLabel($pk, $label)
    {
        $this->setOptions(array(
                              'label' => $label
                          ));

        return $this->getClient()
                    ->post($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

}