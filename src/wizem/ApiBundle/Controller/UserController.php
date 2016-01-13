<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * User controller.
 *
 */
class UserController extends FOSRestController
{
    /**
     * Get an User for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an User for a given id",
     *   output = "UserBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param int     $id      the user id
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUserAction($id)
    {
        $user = $this->getUserOr404($id);

        $formatedUser = $this->container->get('wizem_api.user.handler')->getFormatedUser(
            $user
        );

        return $formatedUser;
    }

    /**
     * Fetch an User or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return User $user
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getUserOr404($id)
    {
        if (!($user = $this->container->get('wizem_api.user.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The user #{$id} was not found");
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The user \'%s\' was not found.',$id));
        }

        return $user;
    }

    /**
     * Fetch an Event or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return User
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getEventOr404($eventId)
    {
        $id = $eventId;
        if (!($event = $this->container->get('wizem_api.event.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The event #{$id} was not found");
            throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('The event \'%s\' was not found.',$id));
        }

        return $event;
    }

    /**
     * Get all Users
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets all Users",
     *   output = "UserBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no User"
     *   }
     * )
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    // public function getUsersAction()
    // {
    //     if (!($users = $this->container->get('wizem_api.user.handler')->getAll())) {
    //         throw new HttpException(Codes::HTTP_NOT_FOUND, sprintf('No user'));
    //     }

    //     return $users;
    // }

    /**
     * Allow connexion for a user 
     *
     * @ApiDoc(
     *   resource = true,
     *   parameters={
     *      {"name"="username", "dataType"="integer", "required"=true, "description"="username of the user"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="password of the user"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return User $user
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function postUserLoginAction(Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== User login from API begin ===== ");

        $user = $this->container->get('wizem_api.user.handler')->connect(
            $request->request->all()
        );

        $apiLogger->info(" ===== User login from API ending ===== ");
        return $user;
    }

    /**
     * Create a new User from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "wizem\UsersBundle\Form\UsersType",
     *   parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="username of the user"},
     *      {"name"="email", "dataType"="email", "required"=true, "description"="email of the user"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="password of the user"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     *
     */
    public function postUserAction(Request $request)
    {
        $email = $request->request->all()['email'];

        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== New User from API begin ===== ");
        $apiLogger->info("User ", array("user" => $email));

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $apiLogger->error("Email not allowed");
            $apiLogger->info(" ===== New User from API ending ===== ");
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "Email not allowed");
        }

        // Create a new user through the user handler
        $newUser = $this->container->get('wizem_api.user.handler')->create(
            $request->request->all()
        );

        $apiLogger->info(" ===== New User from API ending ===== ");
        return $newUser;
    }

    /**
     * Update an user for a given id.
     *
     * @ApiDoc(
     *      parameters={
     *          {"name"="firstname", "dataType"="text", "required"=false, "description"="Firstname of the user"},
     *          {"name"="lastname", "dataType"="text", "required"=false, "description"="Lastname of the user"},
     *          {"name"="birthDate", "dataType"="text", "required"=false, "description"="Birth date of the user"},
     *          {"name"="place", "dataType"="array", "required"=false, "description"="Place (adresse) of the user"},
     *          {"name"="notification", "dataType"="array", "required"=false, "description"="Boolean if user allow notifications"},
     *          {"name"="image", "dataType"="text", "required"=false, "description"="Image of user"},
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
     * @return User     $newUser
     */
    public function putUserAction($id, Request $request)
    {
        $user = $this->getUserOr404($id);

        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Update User from API begin ===== ");
        $apiLogger->info("User #{$user->getId()}");

        $newUser = $this->container->get('wizem_api.user.handler')->update(
            $request->request->all(),
            $user
        );

        $apiLogger->info(" ===== Update User from API ending ===== ");

        return $newUser;
    }

    /**
     * Delete an User for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   output = "EventBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the User is not found"
     *   }
     * )
     *
     * @param int     $id      the User id
     *
     * @return array
     *
     */
    public function deleteUserAction($id)
    {
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Deleting user from API begin ===== ");

        $user = $this->getUserOr404($id);
        
        $response = $this->container->get('wizem_api.user.handler')->delete($user->getId());

        $apiLogger->info(" ===== Deleting event from API ending ===== ");

        return $response;
    }

    /**
     * Get all friends for an user
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no User"
     *   }
     * )
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUsersFriendsAction($id)
    {
        $user = $this->getUserOr404($id);

        if (!($users = $this->container->get('wizem_api.user.handler')->getAllFriends($user))) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, "No friend for this user");
        }

        return $users;
    }

    /**
     * Get all friends for an user who can be invited for an event
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when no User"
     *   }
     * )
     *
     * @param int $userId id of the user
     * @param int $eventId id of the event
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getUsersEventFriendsAction($userId, $eventId)
    {
        $user = $this->getUserOr404($userId);
        $event = $this->getEventOr404($eventId);

        if (!($users = $this->container->get('wizem_api.user.handler')->getAllUsersEvent($user, $event) )) {
            throw new HttpException(Codes::HTTP_NOT_FOUND, "No user for this event");
        }

        return $users;
    }

    /**
     * Add a friend for an user
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "wizem\UsersBundle\Form\UsersType",
     *   parameters={
     *      {"name"="username", "dataType"="integer", "required"=true, "description"="username of the new friend"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the friend no exists"
     *   }
     * )
     *
     * @param int $id id of the user
     * @param Request $request the request object
     *
     * @return $friend
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     *
     */
    public function postUsersFriendsAction($id, Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Adding friend from API begin ===== ");

        $user = $this->getUserOr404($id);
        $apiLogger->info("User #{$user->getId()}");

        $friend = $this->container->get('wizem_api.user.handler')->addFriend(
            $user,
            $request->request->all()
        );

        $apiLogger->info(" ===== Adding friend from API ending ===== ");
        return $friend;
    }

    /**
     * Delete an friend of a user for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   output = "EventBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the User is not found"
     *   }
     * )
     *
     * @param int     $id       the User id
     * @param int     $friendId id of the friend to delete
     *
     * @return array
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function deleteUsersFriendsAction($id, $friendId)
    {
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Deleting friend from API begin ===== ");

        $user = $this->getUserOr404($id);
        $friend = $this->getUserOr404($friendId);
        
        $response = $this->container->get('wizem_api.user.handler')->deleteFriend($user, $friend);

        return $response;
    }

    /**
     * Confirm presence or not in an event for an user
     *
     * @ApiDoc(
     *   resource = true,
     *   parameters={
     *      {"name"="confirm", "dataType"="boolean", "required"=true, "description"="Confirmation or not for the participation of the event"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the friend no exists"
     *   }
     * )
     *
     * @param int $id id of the user
     * @param Request $request the request object
     *
     * @return $friend
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     *
     */
    public function postUserEventConfirmAction($userId, $eventId,Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Confirmation from user for an event from API begin ===== ");
        
        $user = $this->getUserOr404($userId);
        $event = $this->getEventOr404($eventId);

        $apiLogger->info("User #{$user->getId()} want to confirm for Event #{$event->getId()}");

        $confirmation = $this->container->get('wizem_api.user.handler')->confirm(
            $user,
            $event,
            $request->request->all()
        );

        $apiLogger->info(" ===== Confirmation from user for an event from API ending ===== ");
        return $confirmation;
    }

    /**
     * Valid a vote by the host of the event 
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "wizem\UsersBundle\Form\UsersType",
     *   parameters={
     *      {"name"="date", "dataType"="integer", "required"=true, "description"="Id of the date to vote"},
     *      {"name"="place", "dataType"="integer", "required"=true, "description"="Id of the place to vote"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the friend no exists"
     *   }
     * )
     *
     * @param int $id id of the user
     * @param Request $request the request object
     *
     * @return $friend
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     *
     */
    public function postUserEventVoteAction($userId, $eventId,Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Valid a vote from host from API begin ===== ");
        
        if(!isset($request->request->all()['date']) && !isset($request->request->all()['place'])){
            $apiLogger->info("At least 'date' or 'place' is required.");
            $apiLogger->info(" ===== Valid a vote from host from API ending ===== ");
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "At least 'date' or 'place' is required.");
        }

        $user = $this->getUserOr404($userId);
        $event = $this->getEventOr404($eventId);

        $apiLogger->info("User #{$user->getId()}, Event #{$event->getId()}");

        $vote = $this->container->get('wizem_api.user.handler')->validVote(
            $user,
            $event,
            $request->request->all()
        );

        $apiLogger->info(" ===== Valid a vote from host from API ending ===== ");
        return $vote;
    }
}
