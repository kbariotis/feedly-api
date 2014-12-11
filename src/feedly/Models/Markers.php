<?php

namespace feedly\Models;

class Markers extends FeedlyModel
{

    public function __construct($token)
    {
        parent::__construct($token);

        $this->setEndpoint('/v3/markers');
    }

    public function get($pk)
    {
        return $this->getClient()
                    ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/' . $pk);
    }

    public function getUnreadCount()
    {
        return $this->getClient()
                    ->get($this->getApiBaseUrl() . $this->getEndpoint() . '/counts');
    }

    public function markArticleAsRead($body)
    {
        $this->setOptions(array(
                              'action'   => 'markAsRead',
                              'entryIds' => $body,
                              'type'     => 'entries'
                          ));

        return $this->getClient()
                    ->post($this->getApiBaseUrl() . $this->getEndpoint());
    }

    public function markArticleAsUnread($body)
    {
        $this->setOptions(array(
                              'action'   => 'keepUnread',
                              'entryIds' => $body,
                              'type'     => 'entries'
                          ));

        return $this->getClient()
                    ->post($this->getApiBaseUrl() . $this->getEndpoint());
    }

    public function markFeedAsRead($body)
    {
        $this->setOptions(array(
                              'action'  => 'markAsRead',
                              'feedIds' => $body,
                              'type'    => 'feeds'
                          ));

        return $this->getClient()
                    ->post($this->getApiBaseUrl() . $this->getEndpoint());
    }

    public function markCategoryAsRead($body)
    {
        $this->setOptions(array(
                              'action'  => 'markAsRead',
                              'feedIds' => $body,
                              'type'    => 'categories'
                          ));

        return $this->getClient()
                    ->post($this->getApiBaseUrl() . $this->getEndpoint());
    }

}
