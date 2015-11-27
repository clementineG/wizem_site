<?php

namespace wizem\ApiBundle\Exception;

class InvalidUserException extends \RuntimeException
{
    protected $user;

    public function __construct($message, $user = null)
    {
        parent::__construct($message);
        $this->user = $user;
    }

    /**
     * @return array|null
     */
    public function getUser()
    {
        return $this->user;
    }
}