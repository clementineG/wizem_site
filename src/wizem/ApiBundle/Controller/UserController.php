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
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($user = $this->container->get('wizem_api.user.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
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
     * @throws NotFoundHttpException when User not exist
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
     *   input = "wizem\UsersBundle\Form\UsersType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postUserLoginAction(Request $request)
    {
        try {
            // Create a new item through the item handler
            $newUser = $this->container->get('wizem_api.user.handler')->connect(
                $request->request->all()
            );



            $routeOptions = array(
                'id' => $newUser->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_event_get_event', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Create an new Users from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "wizem\UsersBundle\Form\UsersType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postUserAction(Request $request)
    {

        // /* Log */
        // $name = $request->attributes->get('_controller');
        // $apiLogger = $this->container->get('api_logger');
        // $apiLogger->info("API Log", array("Action" => $request->request->all()));

        try {
            // Create a new item through the item handler
            $newUser = $this->container->get('wizem_api.user.handler')->create(
                $request->request->all()
            );


            $routeOptions = array(
                'id' => $newUser->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_user_get_user', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();

        } catch (InvalidUserException $exception) {

            return $exception->getUser();
        }
    }

    /**
     * Delete an User for a given id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an User for a given id",
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
}
