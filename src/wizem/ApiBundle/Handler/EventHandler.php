<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use wizem\ApiBundle\Exception\InvalidFormException;
use wizem\ApiBundle\Form\EventType;

use wizem\EventBundle\Entity\Event;
use wizem\EventBundle\Entity\Date;
use wizem\EventBundle\Entity\Place;

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
    public function getAllUserEvents($user)
    {
        $userEvents = $this->om->getRepository("wizemUserBundle:UserEvent")->findByUser($user->getId());
        
        $tabEvents = array();

        foreach ($userEvents as $userEvent) {

            $date  = $this->om->getRepository("wizemEventBundle:Date")->findOneBy(array("event" => $userEvent->getEvent()->getId(), "final" => true));
            $place = $this->om->getRepository("wizemEventBundle:Place")->findOneBy(array("event" => $userEvent->getEvent()->getId(), "final" => true));

            $tabEvents[] = array(
                "idEvent"    => $userEvent->getEvent()->getId(),
                "typeEvent"  => $userEvent->getEvent()->getTypeEvent()->getName(),
                "dateEvent"  => $date ? $date->getDate() : null,
                "placeEvent" => $place ? $place->getAdress() : null,
            );
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

        $userId = $parameters['userId'];
        unset($parameters['userId']);

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
    public function update(array $parameters, $event, $user)
    {
        $this->checkIfUserHostEvent($event, $user);

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

        unset($parameters['userId']);

        // Gestion des dates si il y en a
        $tabDate = array();
        if(isset($parameters['date'])){
            foreach ($parameters['date'] as $date) {
                $tabDate[] = $date;
            }
            unset($parameters['date']);
        }

        // Gestion des lieux si il y en a
        $tabPlace = array();
        if(isset($parameters['place'])){
            foreach ($parameters['place'] as $place) {
                $tabPlace[] = $place;
            }
            unset($parameters['place']);
        }

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {

            $event = $form->getData();


            //////////// supprimer les anciennes dates ? places ?


            foreach ($tabDate as $date) {
                $newDate = new Date();
                $dateObject = \Datetime::createFromFormat('Y-m-d H:i:s', $date);    
                $newDate->setDate($dateObject);
                $newDate->setEvent($event);
                $newDate->setFinal(0);
                $this->om->persist($newDate);
                $event->addDate($newDate);
            }

            foreach ($tabPlace as $place) {
                $newPlace = new Place();
                $newPlace->setAddress($place);

                // Initialisation des coordonnées
                $coords = $newPlace->getCoords($place);
                $newPlace->setLat($coords['lat']);
                $newPlace->setLng($coords['lng']);

                $newPlace->setEvent($event);
                $newPlace->setFinal(0);
                $this->om->persist($newPlace);
                $event->addPlace($newPlace);
            }


            $this->om->persist($event);
            $this->om->flush();

            return $event;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Add friends for an event.
     *
     * @param array $parameters
     *
     * @return Event
     */
    public function addFriends(array $parameters, $event, $user)
    {
        $this->checkIfUserHostEvent($event, $user);

        $this->logger->info("Begin adding friends to event #{$event->getId()}, hosted by user #{$user->getId()}");

        foreach ($parameters['users'] as $friendId) {

            $friend = $this->container->get('wizem_api.user.handler')->get($friendId);

            // TODO : vérif si le friend est bien ami avec user et s'il n'est pas déjà dans l'évent  
            // Si le friend passe ici c'est qu'il n'est pas dans l'event et qu'il est bien amis avec l'host

            // Création de la table User_Event qui fait la liaison entre l'user et l'évenement 
            $userEvent = new UserEvent(); 
            $userEvent->setEvent($event);
            $userEvent->setUser($friend);
            $userEvent->setHost(0);

            $this->om->persist($userEvent);
            $this->om->flush();
            $this->logger->info("Adding friend #{$userEvent->getUser()->getId()} in UserEvent #{$userEvent->getId()} OK");
        }
        
        return $event;
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

    /**
     * Check if user is the host of the event.
     *
     * @param Event         $event
     * @param array         $parameters
     * @param String        $method
     *
     * @return Event
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function checkIfUserHostEvent($event, $user)
    {
        $userEvent = $this->om->getRepository("wizemUserBundle:UserEvent")->findOneBy(array("event" => $event->getId(), "user" => $user->getId()));

        if(!$userEvent){
            throw new AccessDeniedException('User is not allowed to access this event');
        }
    }


}