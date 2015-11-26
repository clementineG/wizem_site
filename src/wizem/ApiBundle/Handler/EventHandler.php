<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

use wizem\EventBundle\Form\EventType;
use wizem\EventBundle\Entity\Event;

use wizem\ApiBundle\Exception\InvalidFormException;

class EventHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $logger;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $logger)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->logger = $logger;
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
     * Get an event.
     *
     * @return EventInterface
     */
    public function getAll()
    {
        $this->logger->info("getAll");

        return $this->repository->findAll();
    }

    /**
     * Create a new Event.
     *
     * @param array $parameters
     *
     * @return EventInterface
     */
    public function post(array $parameters)
    {
        $event = new $this->entityClass();

        // Process form does all the magic, validate and hydrate the event object.
        return $this->processForm($event, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param eventInterface $event
     * @param array         $parameters
     * @param String        $method
     *
     * @return EventInterface
     *
     * @throws \ApiBundle\Exception\InvalidFormException
     */
    private function processForm(Event $event, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new EventType(), $event, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $event = $form->getData();
            $this->om->persist($event);
            $this->om->flush($event);

            return $event;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Delete an Event.
     *
     * @param mixed $id
     *
     * @return ItemInterface
     */
    public function delete($id)
    {
        $event = $this->repository->find($id);

        $this->om->remove($event);
        $this->om->flush();

        return $id;
    }


}