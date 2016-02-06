<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Event controller.
 *
 */
class EventController extends FOSRestController
{
    /**
     * Get all type of events
     *
     * @ApiDoc(
     *   resource = true,
     *   output = "EventBundle\Entity\TypeEvent",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id      the event id
     *
     * @return Event
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getEventsTypesAction()
    {
        if (!($eventTypes = $this->container->get('wizem_api.event.handler')->getAllTypes())) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, "No TypeEvent found.");
        }

        return $eventTypes;
    }

    /**
     * Get an Event for a given id.
     *
     * @ApiDoc(
     *   output = "EventBundle\Entity\Event",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id      the event id
     *
     * @return Event
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUserEventAction($userId, $eventId)
    {
        $event = $this->getEventOr404($eventId);
        $user = $this->getUserOr404($userId);

        $formatedEvent = $this->container->get('wizem_api.event.handler')->getFormatedEvent(
            $event,
            $user
        );

        return $formatedEvent;
    }

    /**
     * Fetch an Event or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return Event
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getEventOr404($id)
    {
        if (!($event = $this->container->get('wizem_api.event.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The event #{$id} was not found");
            $apiLogger->info(" ===== Ending getEventOr404 ===== ");
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The event \'%s\' was not found.',$id));
        }

        return $event;
    }

    /**
     * Fetch an User or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return User
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getUserOr404($userId)
    {
        $id = $userId;
        if (!($user = $this->container->get('wizem_api.user.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The user #{$id} was not found");
            $apiLogger->info(" ===== Ending getUserOr404 ===== ");
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The user \'%s\' was not found.',$id));
        }

        return $user;
    }

    /**
     * Get all Events for a user
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no event"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", nullable=true, description="Offset from which event to start listing.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", nullable=true, description="How many events to return.")
     * 
     * @param   int   $id     id of the user
     *
     * @return  array
     *
     * @throws  Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUserEventsAction($id, ParamFetcherInterface $paramFetcher)
    {
        $user = $this->getUserOr404($id);

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');

        if (!($events = $this->container->get('wizem_api.event.handler')->getAllUserEvents($user, $limit, $offset))) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('No event for the user \'%s\'.', $id));
        }

        return $events;
    }

    /**
     * Get all users for an event
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no event"
     *   }
     * )
     * 
     * @param   int     $id     id of the event
     *
     * @return  array
     *
     * @throws  Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getEventUsersAction($id)
    {
        $event = $this->getEventOr404($id);

        if (!($users = $this->container->get('wizem_api.event.handler')->getAllEventUsers($event))) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('No user for the event \'%s\'.', $id));
        }

        return $users;
    }

    /**
     * Create an new empty Event from the submitted data, with only the type. Then use PUT method to upload the event.
     *
     * @ApiDoc(
     *      resource = true,
     *      parameters={
     *          {"name"="userId", "dataType"="integer", "required"=true, "description"="Id of the user who create the event"},
     *          {"name"="typeevent", "dataType"="integer", "required"=true, "description"="Id of the type event"},
     *      },
     *      statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *      }
     * )
     *
     * @param Request $request the request object
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function postEventAction(Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== New Event from API begin ===== ");
        $apiLogger->info("Event ", array("event" => $request->request->all()));

        $userId = $request->request->all()['userId'];
        $user = $this->getUserOr404($userId);

        // Create a new event through the event handler
        $newEvent = $this->container->get('wizem_api.event.handler')->create(
            $request->request->all(),
            $user
        );

        $apiLogger->info("Event #{$newEvent->getId()} created");
        $apiLogger->info(" ===== New Event from API ending ===== ");
        return $newEvent->getId();
    }

    /**
     * Update an Event for a given id.
     *
     * @ApiDoc(
     *      parameters={
     *          {"name"="userId", "dataType"="integer", "required"=true, "description"="Id of the user who want to update this event"},
     *          {"name"="description", "dataType"="text", "required"=false, "description"="Description of the event"},
     *          {"name"="place", "dataType"="array", "required"=false, "description"="array of one or max 3 places : {'place' : {'4' : $place, 'date1' : $place}}"},
     *          {"name"="date", "dataType"="array", "required"=false, "description"="array of one or max 3 dates : {'date' : {'4' : $date, 'date1' : $date}} "},
     *          {"name"="shoppingItem", "dataType"="array", "required"=false, "description"="array of items : { 'shoppingItem' : { 'item1' : { 'name' : $name, 'quantity' : $quantity }, '5' : { 'name' : $name, 'quantity' : $quantity } } } "},
     *      },
     *      statusCodes = {
     *         201 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *      }
     * )
     *
     * @param mixed $id
     * @param Request $request the request object
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function putEventAction(Request $request, $id)
    {   
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Update Event from API begin ===== ");

        $event = $this->getEventOr404($id);
        $userId = $request->request->all()['userId']; 
        $user = $this->getUserOr404($userId);

        $newEvent = $this->container->get('wizem_api.event.handler')->update(
            $request->request->all(),
            $event,
            $user
        );

        $apiLogger->info(" ===== Update Event from API begin ===== ");
        return $newEvent;
    }

    /**
     * Add friends to the event for a given id
     *
     * @ApiDoc(
     *      resource = true,
     *      parameters={
     *          {"name"="userId", "dataType"="integer", "required"=true, "description"="Id of the user who want to update this event"},
     *          {"name"="users", "dataType"="string", "required"=true, "description"="array of all users invited : 'users' : { '1' : 1, '2' :  2} "},
     *      },
     *      statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *      }
     * )
     *
     * @param Request $request the request object
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function postEventUserAction($id, Request $request)
    {
        $userId = $request->request->all()['userId']; 
        
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Add friends to event from API begin ===== ");
        $apiLogger->info("Infos : ", array("eventId" => $id,"params" => $request->request->all()));

        $event = $this->getEventOr404($id);
        $user = $this->getUserOr404($userId);

        // Create a new event through the event handler
        $event = $this->container->get('wizem_api.event.handler')->addFriends(
            $request->request->all(),
            $event,
            $user
        );

        $apiLogger->info(" ===== Add friends to event from API ending ===== ");
        return $event;
    }

    /**
     * Delete an event for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id       the event id
     * @param int     $userId   the host user id
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function deleteEventUserAction($id, $userId)
    {
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Deleting event from API begin ===== ");

        $event = $this->getEventOr404($id);
        $user = $this->getUserOr404($userId);
        
        $response = $this->container->get('wizem_api.event.handler')->delete(
            $event,
            $user
        );

        $apiLogger->info(" ===== Deleting event from API ending ===== ");
        return $response;
    }

    /**
     * Add a vote for a date or a place of an event. One of the dateId or placeId is required
     *
     * @ApiDoc(
     *      resource = true,
     *      parameters={
     *          {"name"="date", "dataType"="integer", "required"=false, "description"="Id of the date"},
     *          {"name"="place", "dataType"="integer", "required"=false, "description"="Id of the place"},
     *          {"name"="user", "dataType"="integer", "required"=true, "description"="Id of the user"},
     *          {"name"="event", "dataType"="integer", "required"=true, "description"="Id of the event"},
     *      },
     *      statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *      }
     * )
     *
     * @param Request $request the request object
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function postVoteAction(Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== New Vote from API begin ===== ");
        
        if(!isset($request->request->all()['date']) && !isset($request->request->all()['place'])){
            $apiLogger->info("At least 'date' or 'place' is required.");
            $apiLogger->info(" ===== New Vote from API ending ===== ");
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "At least 'date' or 'place' is required.");
        }

        $eventId = $request->request->all()['event'];
        $userId = $request->request->all()['user'];
        
        $event = $this->getEventOr404($eventId);
        $user = $this->getUserOr404($userId);
        
        $apiLogger->info("User #{$userId}");

        // Create a new event through the event handler
        $vote = $this->container->get('wizem_api.event.handler')->vote(
            $request->request->all(),
            $event,
            $user
        );

        $apiLogger->info(" ===== New Vote from API ending ===== ");
        return $vote;
    }

    /**
     * Get all date votes for an event
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id      the event id
     *
     * @return Vote
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUserEventsVotesDateAction($userId, $eventId)
    {
        $event = $this->getEventOr404($eventId);
        $user = $this->getUserOr404($userId);

        if (!($votes = $this->container->get('wizem_api.event.handler')->getAllEventVotesDate($event, $user))) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, "No Vote found.");
        }

        return $votes;
    }

    /**
     * Get all place votes for an event
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id      the event id
     *
     * @return Vote
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUserEventsVotesPlaceAction($userId, $eventId)
    {
        $event = $this->getEventOr404($eventId);
        $user = $this->getUserOr404($userId);

        if (!($votes = $this->container->get('wizem_api.event.handler')->getAllEventVotesPlace($event, $user))) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, "No Vote found.");
        }

        return $votes;
    }
}
