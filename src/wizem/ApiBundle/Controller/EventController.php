<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Item controller.
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
     * @Annotations\View(templateVar="item")
     *
     * @param int     $id      the item id
     *
     * @return array
     *
     * @throws NotFoundHttpException when item not exist
     */
    public function getEventAction($id)
    {
        $item = $this->getOr404($id);

        return $item;
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
     * @Annotations\View(templateVar="event")
     *
     * @return array
     *
     * @throws NotFoundHttpException when item not exist
     */
    public function getEventsAction()
    {
        if (!($events = $this->container->get('wizem_api.event.handler')->getAll())) {
            throw new NotFoundHttpException(sprintf('No event'));
        }

        return $events;
    }

}
