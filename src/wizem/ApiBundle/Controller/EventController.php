<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use wizem\ApiBundle\Exception\InvalidFormException;

/**
 * Event controller.
 *
 */
class EventController extends FOSRestController
{
    /**
     * Get an Event for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an Event for a given id",
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
     * @throws NotFoundHttpException when event not exist
     */
    public function getEventAction($id)
    {
        $event = $this->getOr404($id);

        return $event;
    }

    /**
     * Fetch an Event or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return Event
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($event = $this->container->get('wizem_api.event.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $event;
    }

    /**
     * Get all Events for a user
     *
     * @ApiDoc(
     *   resource = true,
     *   parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id of the user to get his events"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no event"
     *   }
     * )
     *
     * @return array
     *
     * @throws NotFoundHttpException when event not exist
     */
    public function getUserEventsAction($id)
    {
        //return "e";
        return $id;

        if (!($events = $this->container->get('wizem_api.event.handler')->getAll())) {
            throw new NotFoundHttpException(sprintf('No event'));
        }

        return $events;
    }

    /**
     * Create an new empty Event from the submitted data, with only the type. Then use PUT method to upload the event.
     *
     * @ApiDoc(
     *      resource = true,
     *      input = "wizem\EventBundle\Form\EventType",
     *      parameters={
     *      },     
     *      statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *      }
     * )
     *
     * @param Request $request the request object
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function postEventAction(Request $request)
    {

        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== New Event from API begin ===== ");
        $apiLogger->info("Event ", array("event" => $request->request->all()));

        try {
            // Create a new event through the event handler
            $newEvent = $this->container->get('wizem_api.event.handler')->create(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newEvent->getId(),
                '_format' => $request->get('_format')
            );

            $apiLogger->info(" ===== New Event from API ending ===== ");
            return $this->routeRedirectView('api_event_get_event', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update an Event for a given id.
     *
     * @ApiDoc(
     *      parameters={
     *          {"name"="description", "dataType"="text", "required"=false, "description"="Description of the event"},
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
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function putEventAction($id, Request $request)
    {   
        try {
            // Create a new item through the item handler
            $newEvent = $this->container->get('wizem_api.event.handler')->update(
                $request->request->all(),
                $id
            );

            $routeOptions = array(
                'id' => $newEvent->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_event_get_event', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Delete an event for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an Event for a given id",
     *   output = "EventBundle\Entity\Event",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @param int     $id      the event id
     *
     * @return array
     *
     * @throws NotFoundHttpException when event not exist
     */
    public function deleteEventAction($id)
    {
        $event = $this->getOr404($id);
        
        $response = $this->container->get('wizem_api.event.handler')->delete($event->getId());

        return $response;
    }
}
