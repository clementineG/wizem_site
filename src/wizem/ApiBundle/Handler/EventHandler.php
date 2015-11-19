<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

class EventHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get an Event.
     *
     * @param mixed $id
     *
     * @return EventInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get an Item.
     *
     * @return EventInterface
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

}

?>