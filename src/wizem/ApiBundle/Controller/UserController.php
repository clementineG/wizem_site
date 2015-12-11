<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $user = $this->getOr404($id);

        return $user;
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
    protected function getOr404($id)
    {
        if (!($user = $this->container->get('wizem_api.user.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.',$id));
        }

        return $user;
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
    public function getUsersAction()
    {
        if (!($users = $this->container->get('wizem_api.user.handler')->getAll())) {
            throw new NotFoundHttpException(sprintf('No user'));
        }

        return $users;
    }

    /**
     * Allow connexion for a user 
     *
     * @ApiDoc(
     *   resource = true,
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

            $routeOptions = array(
                'id' => $user->getId(),
                '_format' => $request->get('_format')
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

            $routeOptions = array(
                'id' => $newUser->getId(),
                '_format' => $request->get('_format')
            );

            $apiLogger->info(" ===== New User from API ending ===== ");
            return $this->routeRedirectView('api_user_get_user', $routeOptions, Codes::HTTP_CREATED);

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

        return $request->request->all();
        // try {
        //     // Create a new item through the item handler
        //     $newEvent = $this->container->get('wizem_api.event.handler')->update(
        //         $request->request->all(),
        //         $id
        //     );

        //     $routeOptions = array(
        //         'id' => $newEvent->getId(),
        //         '_format' => $request->get('_format')
        //     );

        //     return $this->routeRedirectView('api_event_get_event', $routeOptions, Codes::HTTP_CREATED);

        // } catch (InvalidFormException $exception) {

        //     return $exception->getForm();
        // }
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
        $user = $this->getOr404($id);
        
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
        // if (!($users = $this->container->get('wizem_api.user.handler')->getAll())) {
        //     throw new NotFoundHttpException(sprintf('No user'));
        // }

        return $id;
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
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     *
     * @throws InvalidFormException when form not valid
     * @throws InvalidUserException when User not exist
     *
     */
    public function postUsersFriendsAction($id, Request $request)
    {
        /* Log */
        // $apiLogger = $this->container->get('api_logger');
        // $apiLogger->info(" ===== New User from API begin ===== ");
        // $apiLogger->info("User ", array("user" => $email));

        return $request->request->all();

        // try {
        //     // Create a new user through the user handler
        //     $newUser = $this->container->get('wizem_api.user.handler')->create(
        //         $request->request->all()
        //     );

        //     $routeOptions = array(
        //         'id' => $newUser->getId(),
        //         '_format' => $request->get('_format')
        //     );

        //     $apiLogger->info(" ===== New User from API ending ===== ");
        //     return $this->routeRedirectView('api_user_get_user', $routeOptions, Codes::HTTP_CREATED);

        // } catch (InvalidFormException $exception) {

        //     return $exception->getForm();

        // } catch (Exception $exception) {
            
        //     return $exception->getUser();
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
     * @param int     $id      the User id
     *
     * @return array
     *
     * @throws NotFoundHttpException when User not exist
     */
    public function deleteUsersFriendsAction($id,$friendId)
    {
        // $user = $this->getOr404($id);
        
        // $response = $this->container->get('wizem_api.user.handler')->delete($user->getId());

        return $friendId;
    }
}
