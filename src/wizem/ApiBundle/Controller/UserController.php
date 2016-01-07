<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use wizem\ApiBundle\Exception\InvalidUserException;
use wizem\ApiBundle\Exception\InvalidFormException;

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
     * @throws NotFoundHttpException when user not exist
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
     * @throws NotFoundHttpException
     */
    protected function getUserOr404($id)
    {
        if (!($user = $this->container->get('wizem_api.user.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.',$id));
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
     * @throws NotFoundHttpException
     */
    protected function getEventOr404($eventId)
    {
        $id = $eventId;
        if (!($event = $this->container->get('wizem_api.event.handler')->get($id))) {
            $apiLogger = $this->container->get('api_logger');
            $apiLogger->info("The event #{$id} was not found");
            throw new NotFoundHttpException(sprintf('The event \'%s\' was not found.',$id));
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
     * @throws NotFoundHttpException when no user
     */
    // public function getUsersAction()
    // {
    //     if (!($users = $this->container->get('wizem_api.user.handler')->getAll())) {
    //         throw new NotFoundHttpException(sprintf('No user'));
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
     * @throws InvalidFormException when form not valid
     */
    public function postUserLoginAction(Request $request)
    {
        try {
            // Create a new item through the item handler
            $user = $this->container->get('wizem_api.user.handler')->connect(
                $request->request->all()
            );

            return $user;

            //return $this->routeRedirectView('api_user_get_user', $routeOptions, Codes::HTTP_ACCEPTED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
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
     * @throws InvalidFormException when form not valid
     * @throws InvalidUserException when User not exist
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
            throw new InvalidUserException('Email not allowed');
        }

        try {
            // Create a new user through the user handler
            $newUser = $this->container->get('wizem_api.user.handler')->create(
                $request->request->all()
            );

            $apiLogger->info(" ===== New User from API ending ===== ");
            return $newUser;

        } catch (InvalidFormException $exception) {

            return $exception->getForm();

        } catch (Exception $exception) {
            
            return $exception->getUser();
        }
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
    public function putUserAction($id, Request $request)
    {
        $user = $this->getUserOr404($id);

        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Update User from API begin ===== ");
        $apiLogger->info("User #{$user->getId()}");

        try {
            $newUser = $this->container->get('wizem_api.user.handler')->update(
                $request->request->all(),
                $user
            );

            $apiLogger->info(" ===== Update User from API ending ===== ");

            return $newUser->getId();

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
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
     * @throws NotFoundHttpException when User not exist
     */
    public function deleteUserAction($id)
    {
        $user = $this->getUserOr404($id);
        
        $response = $this->container->get('wizem_api.user.handler')->delete($user->getId());

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
     * @throws NotFoundHttpException when no user
     */
    public function getUsersFriendsAction($id)
    {
        $user = $this->getUserOr404($id);

        if (!($users = $this->container->get('wizem_api.user.handler')->getAllFriends($user))) {
            throw new NotFoundHttpException('No friend for this user');
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
     * @throws NotFoundHttpException when no user
     */
    public function getUsersEventFriendsAction($userId, $eventId)
    {
        $user = $this->getUserOr404($userId);
        $event = $this->getEventOr404($eventId);

        if (!($users = $this->container->get('wizem_api.user.handler')->getAllUsersEvent($user, $event) )) {
            throw new NotFoundHttpException('No user for this event');
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
     * @throws InvalidFormException when form not valid
     * @throws NotFoundHttpException when User or friend not exist
     * @throws AccessDeniedException when access denied
     *
     */
    public function postUsersFriendsAction($id, Request $request)
    {
        /* Log */
        $apiLogger = $this->container->get('api_logger');
        $apiLogger->info(" ===== Adding friend from API begin ===== ");

        $user = $this->getUserOr404($id);
        $apiLogger->info("User #{$user->getId()}");
        try {
            $friend = $this->container->get('wizem_api.user.handler')->addFriend(
                $user,
                $request->request->all()
            );

            $apiLogger->info(" ===== Adding friend from API ending ===== ");
            return $friend;

        } catch (Exception $e){
            return $e;
        }
        // } catch (AccessDeniedException $exception) {
            
        //     $apiLogger->info(" ===== Adding friend from API ending ===== ");
        //     return $exception;//->getMessage();

        // } catch (InvalidFormException $exception) {

        //     $apiLogger->info(" ===== Adding friend from API ending ===== ");
        //     return $exception;//->getMessage();

        // } catch (NotFoundHttpException $exception) {
            
        //     $apiLogger->info(" ===== Adding friend from API ending ===== ");
        //     return $exception;//->getMessage();
        // }
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
     * @throws NotFoundHttpException when User or friend not exist
     */
    public function deleteUsersFriendsAction($id, $friendId)
    {
        $user = $this->getUserOr404($id);
        $friend = $this->getUserOr404($friendId);
        
        $response = $this->container->get('wizem_api.user.handler')->deleteFriend($user, $friend);

        return $response;
    }
}
