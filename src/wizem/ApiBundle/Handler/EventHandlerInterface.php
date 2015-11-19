<?php

namespace EventBundle\Handler;

//use EventBundle\Model\EventInterface;

interface EventHandlerInterface
{
    /**
     * Get a Item given the identifier.
     *
     * @api
     *
     * @param mixed $id
     *
     * @return ItemInterface
     */
    public function get($id);

    /**
     * Create a new Item.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return ItemInterface
     */
    //public function post(array $parameters);
}