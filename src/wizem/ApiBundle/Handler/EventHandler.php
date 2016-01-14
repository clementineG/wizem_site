<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Util\Codes;

use wizem\ApiBundle\Form\EventType;
use wizem\ApiBundle\Form\VoteType;

use wizem\EventBundle\Entity\Event;
use wizem\EventBundle\Entity\Date;
use wizem\EventBundle\Entity\Place;
use wizem\EventBundle\Entity\Vote;

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
     * Get an application formated Event.
     *
     * @param Event $event
     */
    public function getFormatedEvent($event, $user)
    {
        $dates = $event->getDate();
        $places = $event->getPlace();

        $this->checkIfUserLinkToEvent($event, $user, false);

        // Check all dates for final date
        $tabDates = array();
        foreach ($dates as $date) {
            $tabDates[] = array("id" => $date->getId(), "date" => $date->getDate());
            if($date->getFinal() == true){
                $finalDate = $date;
            }
        }

        // Check all places for final place
        $tabPlaces = array();
        foreach ($places as $place) {
            $tabPlaces[] = array("id" => $place->getId(), "address" => $place->getAddress(), "lat" => $place->getLat(), "lng" => $place->getLng());
            if($place->getFinal() == true){
                $finalPlace = $place;
            }
        }

        // If no date here : the vote is not finish 
        if(!isset($finalDate)){
            $dateVote = $this->om->getRepository("wizemEventBundle:Vote")->findOneBy(array(
                "event" => $event->getId(),
                "user" => $user->getId() 
            ));
            $hasVotedDate = $dateVote ? ($dateVote->getDate() ? $dateVote->getDate()->getDate() : false ) : false;
        }
        // If no place here : the vote is not finish 
        if(!isset($finalPlace)){
            $placeVote = $this->om->getRepository("wizemEventBundle:Vote")->findOneBy(array(
                "event" => $event->getId(),
                "user" => $user->getId() 
            ));
            $hasVotedPlace = $placeVote ? ($placeVote->getPlace() ? $placeVote->getPlace()->getAddress() : false ) : false;
        }

        $date = array(
            "final" => isset($finalDate) ? array("id" => $finalDate->getId(), "date" => $finalDate->getDate()) : null,
            "dates" => $tabDates,
            "hasVoted" => isset($hasVotedDate) ? $hasVotedDate : null,
        );
        $place = array(
            "final" => isset($finalPlace) ? array("id" => $finalPlace->getId(), "address" => $finalPlace->getAddress(), "lat" => $finalPlace->getLat(), "lng" => $finalPlace->getLng()) : null,
            "places" => $tabPlaces,
            "hasVoted" => isset($hasVotedPlace) ? $hasVotedPlace : null,
        );

        $usersEvent = $this->om->getRepository("wizemUserBundle:UserEvent")->findByEvent($event->getId(), null, 3);

        $tabUsers = array();
        foreach ($usersEvent as $userEvent) {
            $user = $userEvent->getUser();
            $tabUsers[] = array(
                "id" => $user->getId(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "username" => $user->getUsername(),
                "image" => $user->getImage(),
                "state" => $userEvent->getState(),
            );
        }

        return array(
            "typeEvent" => $event->getTypeEvent()->getName(),
            "description" => $event->getDescription(),
            "date" => $date,
            "place" => $place,
            "users" => $tabUsers,
        );
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
     * @return Events
     */
    public function getAllUserEvents($user)
    {
        $userEvents = $this->om->getRepository("wizemUserBundle:UserEvent")->findByUser($user->getId());
        
        $tabEvents = array();

        foreach ($userEvents as $userEvent) {

            $date  = $this->om->getRepository("wizemEventBundle:Date")->findOneBy(array("event" => $userEvent->getEvent()->getId(), "final" => true));
            $place = $this->om->getRepository("wizemEventBundle:Place")->findOneBy(array("event" => $userEvent->getEvent()->getId(), "final" => true));
            $host  = $this->om->getRepository("wizemUserBundle:UserEvent")->getHost($userEvent->getEvent()->getId());

            $tabEvents[] = array(
                "id"    => $userEvent->getEvent()->getId(),
                "type"  => $userEvent->getEvent()->getTypeEvent()->getName(),
                "date"  => $date ? $date->getDate() : null,
                "place" => $place ? $place->getAddress() : null,
                "host" => ($host['firstname'] && $host['lastname']) ? $host['firstname']." ".$host['lastname'] : $host['username']
            );
        }

        return $tabEvents;
    }

    /**
     * Get all users for an event.
     *
     * @return Users
     */
    public function getAllEventUsers($event)
    {
        $userEvents = $this->om->getRepository("wizemUserBundle:UserEvent")->findByEvent($event->getId());
        
        $tabUsers = array();

        foreach ($userEvents as $userEvent) {

            $user = $userEvent->getUser();
            $tabUsers[] = array(
                "id" => $user->getId(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "username" => $user->getUsername(),
                "image" => $user->getImage(),
                "state" => $userEvent->getState(),
            );
        }

        return $tabUsers;
    }

    /**
     * Get all place votes for an event.
     *
     * @return array Votes
     */
    public function getAllEventVotesPlace($event, $user)
    {
        $this->checkIfUserLinkToEvent($event, $user, false);

        $places = $event->getPlace();

        // Check all places
        $tabPlaces = array();
        foreach ($places as $place) {

            // Calcul nb votes
            $placeVotes = $this->om->getRepository("wizemEventBundle:Vote")->findBy(array(
                "event" => $event->getId(),
                "place" => $place->getId() 
            ));

            // check if user has voted for this place or not
            $placeVoteUser = $this->om->getRepository("wizemEventBundle:Vote")->findOneBy(array(
                "event" => $event->getId(),
                "place" => $place->getId(), 
                "user" => $user->getId() 
            ));

            $nbVotes = count($placeVotes);

            $tabPlaces[] = array(
                "id" => $place->getId(), 
                "address" => $place->getAddress(), 
                "lat" => $place->getLat(), 
                "lng" => $place->getLng(),
                "nbVotes" => $nbVotes,
                "hasVoted" => $placeVoteUser ? true : false
            );
        }

        return $tabPlaces;
    }

    /**
     * Get all date votes for an event.
     *
     * @return array Votes
     */
    public function getAllEventVotesDate($event, $user)
    {
        $this->checkIfUserLinkToEvent($event, $user, false);

        $dates = $event->getDate();

        // Check all places
        $tabDates = array();
        foreach ($dates as $date) {
            // Calcul nb votes
            $dateVotes = $this->om->getRepository("wizemEventBundle:Vote")->findBy(array(
                "event" => $event->getId(),
                "date" => $date->getId() 
            ));

            // check if user has voted for this date or not
            $dateVoteUser = $this->om->getRepository("wizemEventBundle:Vote")->findOneBy(array(
                "event" => $event->getId(),
                "date" => $date->getId(), 
                "user" => $user->getId() 
            ));

            $nbVotes = count($dateVotes);

            $tabDates[] = array(
                "id" => $date->getId(),
                "date" => $date->getDate(),
                "nbVotes" => $nbVotes,
                "hasVoted" => $dateVoteUser ? true : false,
            );
        }

        return $tabDates;
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
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
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
                $this->logger->info("The user #{$userId} was not found");
                $this->logger->info(" ===== New Event from API ending ===== ");
                throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The user \'%s\' was not found.',$userId));
            }

            // User_Event creation for for link between user and event
            $userEvent = new UserEvent(); 
            $userEvent->setEvent($event);
            $userEvent->setUser($user);
            // If creation of event, user is automatically the host and he participate 
            $userEvent->setState(1);
            $userEvent->setHost(1);

            $this->om->persist($userEvent);
            $this->om->flush();
            $this->logger->info("Creating associate UserEvent() : #{$userEvent->getId()} ");

            return $event;
        }

        $this->logger->info("Invalid submitted data");
        $this->logger->info(" ===== New Event from API ending ===== ");
        throw new HttpException(Codes::HTTP_BAD_REQUEST, "Invalid submitted data");
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
        $this->checkIfUserLinkToEvent($event, $user, true);

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
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function updateEventProcessForm(Event $event, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new EventType($this->container, $method), $event, array('method' => $method));

        unset($parameters['userId']);

        // Gestion of dates if there is any
        $tabDate = array();
        if(isset($parameters['date'])){
            foreach ($parameters['date'] as $id => $date) {
                $tabDate[] = array("id" => $id, "date" => $date);
            }
            unset($parameters['date']);
        }

        // Gestion of places if there is any
        $tabPlace = array();
        if(isset($parameters['place'])){
            foreach ($parameters['place'] as $id => $place) {
                $tabPlace[] = array("id" => $id, "place" => $place);
            }
            unset($parameters['place']);
        }

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {

            $this->logger->info("Valid form");
            $event = $form->getData();

            foreach ($tabDate as $date) {
                
                $dateObject = \Datetime::createFromFormat('Y-m-d H:i:s', $date['date']);    
                
                if( substr($date['id'], 0, 4) == "date" ){
                    // Create new Date
                    $newDate = new Date();
                    $newDate->setDate($dateObject);
                    $newDate->setEvent($event);
                    $final = count($tabDate) > 1 ? false : true;
                    $newDate->setFinal($final);
                    $this->om->persist($newDate);
                    $event->addDate($newDate);
                    $this->logger->info("New date created : {$date['date']}");
                }else{
                    // Update existing Date
                    $existingDate = $this->om->getRepository("wizemEventBundle:Date")->find($date['id']);
                    $existingDate->setDate($dateObject);
                    $this->om->persist($existingDate);
                    $this->logger->info("Existing date #{$date['id']} updated ");
                }
            }

            foreach ($tabPlace as $place) {

                if( substr($place['id'], 0, 5) == "place" ){
                    // Create new Place
                    $newPlace = new Place();
                    $newPlace->setAddress($place['place']);
                    $coords = $newPlace->getCoords($place['place']);
                    $newPlace->setLat($coords['lat']);
                    $newPlace->setLng($coords['lng']);
                    $newPlace->setEvent($event);

                    $final = count($tabPlace) > 1 ? false : true;
                    $newPlace->setFinal($final);
                    $this->om->persist($newPlace);
                    $event->addPlace($newPlace);
                    $this->logger->info("New place created : {$place['place']}");
                }else{
                    // Update existing Place
                    $existingPlace = $this->om->getRepository("wizemEventBundle:Place")->find($place['id']);
                    $coords = $existingPlace->getCoords($place['place']);
                    $existingPlace->setAddress($place['place']);
                    $existingPlace->setLat($coords['lat']);
                    $existingPlace->setLng($coords['lng']);
                    $this->om->persist($existingPlace);
                    $this->logger->info("Existing place #{$place['id']} updated");
                }
            }

            $this->om->persist($event);
            $this->om->flush();

            return $event;
        }

        $this->logger->info("Invalid submitted data");
        $this->logger->info(" ===== Update Event from API begin ===== ");
        throw new HttpException(Codes::HTTP_BAD_REQUEST, "Invalid submitted data");
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
        $this->checkIfUserLinkToEvent($event, $user, true);

        $this->logger->info("Begin adding friends to event #{$event->getId()}, hosted by user #{$user->getId()}");

        foreach ($parameters['users'] as $friendId) {

            $friend = $this->container->get('wizem_api.user.handler')->get($friendId);

            // If the friend is here, he is not allready in the event, and he is friend with host

            // User_Event creation for for link between user and event
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

        $this->logger->info("Deleting event #{$id} OK");

        $this->om->remove($event);
        $this->om->flush();

        return $id;
    }

    /**
     * Create a vote
     *
     * @param array $parameters
     * @param Event $event
     * @param User $user
     *
     * @return 
     */
    public function vote(array $parameters, $event, $user)
    {
        $this->checkIfUserLinkToEvent($event, $user, false);

        $vote = $this->om->getRepository("wizemEventBundle:Vote")->findOneBy(array("user" => $user->getId(), "event" => $event->getId()));

        if(!$vote){
            $vote = new Vote();
        }else{
            if(!isset($parameters['date'])){
                $parameters['date'] = $vote->getDate() ? $vote->getDate()->getId() : null;
            }
            if(!isset($parameters['place'])){
                $parameters['place'] = $vote->getPlace() ? $vote->getPlace()->getId() : null;
            }
        }

        return $this->updateVoteProcessForm($vote, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param array     $parameters
     * @param Event     $event
     * @param User      $user
     *
     * @return Event
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function updateVoteProcessForm(Vote $vote, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new VoteType(), $vote, array('method' => $method));

        $form->submit($parameters, 'PATCH' !== $method);
        $this->logger->info("Submit form with params :", $parameters);

        if ($form->isValid()) {

            $vote = $form->getData();

            $this->om->persist($vote);
            $this->om->flush();

            $this->logger->info("Vote form valid");
            return $vote;
        }

        $this->logger->info("Invalid submitted data");
        $this->logger->info(" ===== New Vote from API ending ===== ");
        throw new HttpException(Codes::HTTP_BAD_REQUEST, "Invalid submitted data");
    }

    /**
     * Check if user is link to the event.
     *
     * @param Event         $event
     * @param User          $user
     * @param boolean       $host
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function checkIfUserLinkToEvent($event, $user, $host = false)
    {
        $userEvent = $this->om->getRepository("wizemUserBundle:UserEvent")->findOneBy(array("event" => $event->getId(), "user" => $user->getId()));

        if(!$userEvent || ($host == true && $userEvent->getHost() == false) ){
            $host = ($host == true ? 'true' : 'false');
            $this->logger->info("User #{$user->getId()} not allowed to access Event #{$event->getId()} (search for host : {$host}) ");
            throw new HttpException(Codes::HTTP_FORBIDDEN, "User is not allowed to access this event");
        }
    }
}