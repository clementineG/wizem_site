<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use wizem\ApiBundle\Exception\InvalidFormException;
use wizem\ApiBundle\Form\EventType;

use wizem\EventBundle\Entity\Event;

use wizem\UserBundle\Entity\UserEvent;


class EventHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $container;
    private $logger;

    public function __construct(
        ObjectManager $om, 
        $entityClass, 
        FormFactoryInterface $formFactory, 
        ContainerInterface $container, 
        $logger)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Get an Event.
     *
     * @param mixed $id
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get an event.
     *
     * @return Events
     */
    public function getAll()
    {
        $this->logger->info("getAll");

        return $this->repository->findAll();
    }

    /**
     * Get all types of  event.
     *
     * @return EventType
     */
    public function getAllTypes()
    {
        return $this->om->getRepository("wizemEventBundle:Typeevent")->findAll();
    }

    /**
     * Get all events for a user.
     *
     * @return EventType
     */
    public function getAllUserEvents($id)
    {
        $userEvents = $this->om->getRepository("wizemUserBundle:UserEvent")->findByUser($id);
        
        $tabEvents = array();

        foreach ($userEvents as $userEvent) {
            $tabEvents[] = $userEvent->getEvent();
        }

        return $tabEvents;
    }

    /**
     * Create a new Event.
     *
     * @param array $parameters
     *
     * @return Event
     */
    public function create(array $parameters)
    {
        $event = new $this->entityClass();

        // Process form does all the magic, validate and hydrate the event object.
        return $this->createEventProcessForm($event, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param Event         $event
     * @param array         $parameters
     * @param String        $method
     *
     * @return Event
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     * @throws wizem\ApiBundle\Exception\NotFoundHttpException
     */
    private function createEventProcessForm(Event $event, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new EventType(), $event, array('method' => $method));

        $userId = $parameters['user'];
        unset($parameters['user']);

        $form->submit($parameters, 'PATCH' !== $method);
        $this->logger->info("Processing form");
        
        if ($form->isValid()) {

            $event = $form->getData();
            $this->om->persist($event);
            $this->om->flush();

            if (!($user = $this->container->get('wizem_api.user.handler')->get($userId))) {
                throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.',$userId));
            }

            // Création de la table User_Event qui fait la liaison entre l'user et l'évenement 
            $userEvent = new UserEvent(); 
            $userEvent->setEvent($event);
            $userEvent->setUser($user);
            // Si on est dans la création d'un évenement, l'user participe forcement, et est l'hote
            $userEvent->setState(1);
            $userEvent->setHost(1);

            $this->om->persist($userEvent);
            $this->om->flush();
            $this->logger->info("Creating associate UserEvent() : #{$userEvent->getId()} ");

            return $event;
        }

        $this->logger->info("Invalid submitted data");
        $this->logger->info(" ===== New Event from API ending ===== ");
        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Upadte a Event.
     *
     * @param array $parameters
     *
     * @return Event
     */
    public function update(array $parameters, $id)
    {
        $event = $this->get($id);
        //$event = new $this->entityClass();

        // Process form does all the magic, validate and hydrate the event object.
        return $this->updateEventProcessForm($event, $parameters, 'PUT');
    }

    /**
     * Processes the form.
     *
     * @param Event         $event
     * @param array         $parameters
     * @param String        $method
     *
     * @return Event
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    private function updateEventProcessForm(Event $event, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new EventType($this->container, $method), $event, array('method' => $method));

        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $event = $form->getData();
            $this->om->persist($event);
            $this->om->flush();

            return $event;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Delete an Event.
     *
     * @param mixed $id
     *
     * @return mixed $id
     */
    public function delete($id)
    {
        $event = $this->repository->find($id);

        $this->om->remove($event);
        $this->om->flush();

        return $id;
    }


}