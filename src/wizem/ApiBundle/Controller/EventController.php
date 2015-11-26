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
     * @Rest\View(templateVar="event")
     *
     * @param int     $id      the event id
     *
     * @return array
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
     * @return EventInterface
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
     * Get all Events
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets all Events",
     *   output = "EventBundle\Entity\Event",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no event"
     *   }
     * )
     *
     * @Rest\View(templateVar="event")
     *
     * @return array
     *
     * @throws NotFoundHttpException when event not exist
     */
    public function getEventsAction()
    {
        if (!($events = $this->container->get('wizem_api.event.handler')->getAll())) {
            throw new NotFoundHttpException(sprintf('No event'));
        }

        return $events;
    }

    /**
     * Create an new Event from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "wizem\EventBundle\Form\EventType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Rest\View(
     *  template = "EventBundle:Event:post.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postEventAction(Request $request)
    {

        // /* Log */
        // $name = $request->attributes->get('_controller');
        // $apiLogger = $this->container->get('api_logger');
        // $apiLogger->info("API Log", array("Action" => $request->request->all()));

        try {
            // Create a new item through the item handler
            $newEvent = $this->container->get('wizem_api.event.handler')->post(
                $request->request->all()
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
