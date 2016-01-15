<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Discussion controller.
 *
 */
class DiscussionController extends FOSRestController
{
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
     * Fetch a Discussion or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return Discussion
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getDiscussionOr404($id)
    {
        if (!($discussion = $this->container->get('wizem_api.discussion.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The discussion #{$id} was not found");
            $apiLogger->info(" ===== Ending getDiscussionOr404 ===== ");
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The discussion \'%s\' was not found.',$id));
        }

        return $discussion;
    }

    /**
     * Get a Discussion for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the discussion is not found"
     *   }
     * )
     *
     * @param int     $id      the discussion id
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getDiscussionAction($id)
    {
        $discussion = $this->getDiscussionOr404($id);

        $formatedDiscussion = $this->container->get('wizem_api.discussion.handler')->getFormatedDiscussion(
            $discussion
        );

        return $formatedDiscussion;
    }

    /**
     * Add a message in the discussion
     *
     * @ApiDoc(
     *      resource = true,
     *      parameters={
     *          {"name"="userId", "dataType"="integer", "required"=true, "description"="Id of the user who write the message"},
     *          {"name"="content", "dataType"="text", "required"=true, "description"="The message"},
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
    public function postDiscussionMessageAction($id, Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== New Message from API begin ===== ");

        $userId = $request->request->all()['userId'];
        $user = $this->getUserOr404($userId);
        $discussion = $this->getDiscussionOr404($id);

        // Create a new event through the event handler
        $newMessage = $this->container->get('wizem_api.discussion.handler')->addMessage(
            $request->request->all(),
            $user,
            $discussion
        );

        $apiLogger->info("Message #{$newMessage->getId()} added to discussion #{$discussion->getId()}");
        $apiLogger->info(" ===== New Event from API ending ===== ");
        return $newMessage->getId();
    }

}
