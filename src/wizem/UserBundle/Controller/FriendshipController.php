<?php

namespace wizem\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use wizem\UserBundle\Entity\Friendship;
use wizem\UserBundle\Form\FriendshipType;

/**
 * Friendship controller.
 *
 */
class FriendshipController extends Controller
{

    /**
     * Lists all Friendship entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $confirmed = true;
        $friendship = $em->getRepository("wizemUserBundle:Friendship")->getFriends($user->getId(), $confirmed);

        $friends = array();
        foreach ($friendship as $friend) {
            if($friend->getFriend()->getId() != $user->getId()){
                $friends[] = $friend;
            }
            if($friend->getUser()->getId() != $user->getId()){
                $friends[] = $friend;
            }
        }

        return $this->render('wizemUserBundle:Friendship:index.html.twig', array(
            'friends' => $friends,
        ));
    }
    /**
     * Creates a new Friendship entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Friendship();
        $form = $this->createCreateForm($entity);
        
        var_dump($request->getData());exit();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            var_dump($form->getFriend());exit();

            $userLogger = $this->get("user_logger");
            $userLogger->info("User trying to add '{$username}'");

            if($username != ""){
                
                $um = $this->container->get('fos_user.user_manager');
                $friend = $um->findUserByUsername($username);
                if(!$friend){
                    $userLogger->error("Friend not found");
                    $userLogger->info(" ===== Adding friend from API ending ===== ");
                    throw new HttpException(Codes::HTTP_NOT_FOUND, "Friend not found");
                }
                
                // Test if user and friend are already friends
                $testFriendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $user->getId(), "friend" => $friend->getId()));
                if($testFriendship){
                    $userLogger->error("Users are already friends");
                    $userLogger->info(" ===== Adding friend from API ending ===== ");
                    throw new HttpException(Codes::HTTP_FORBIDDEN, "You are already friends");
                }
                $testFriendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $friend->getId(), "friend" => $user->getId()));
                if($testFriendship){
                    $userLogger->error("Users are already friends");
                    $userLogger->info(" ===== Adding friend from API ending ===== ");
                    throw new HttpException(Codes::HTTP_FORBIDDEN, "You are already friends");
                }

                $friendship = new Friendship(); 
                $friendship->setUser($user);
                $friendship->setFriend($friend);

                $this->om->persist($friendship);
                $this->om->flush();
                    
                $userLogger->info("Friend added");

                return $friend;
            }
        
        $userLogger->error("Invalid username");
        $userLogger->info(" ===== Adding friend from API ending ===== ");
        throw new HttpException(Codes::HTTP_FORBIDDEN, "Invalid username");








            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('friendship_show', array('id' => $entity->getId())));
        }

        return $this->render('wizemUserBundle:Friendship:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Friendship entity.
     *
     * @param Friendship $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Friendship $entity)
    {
        $form = $this->createForm(new FriendshipType(), $entity, array(
            'action' => $this->generateUrl('friendship_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Friendship entity.
     *
     */
    public function newAction()
    {
        $entity = new Friendship();
        $form   = $this->createCreateForm($entity);

        return $this->render('wizemUserBundle:Friendship:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Friendship entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemUserBundle:Friendship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Friendship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('wizemUserBundle:Friendship:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Friendship entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemUserBundle:Friendship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Friendship entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('wizemUserBundle:Friendship:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Friendship entity.
    *
    * @param Friendship $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Friendship $entity)
    {
        $form = $this->createForm(new FriendshipType(), $entity, array(
            'action' => $this->generateUrl('friendship_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Friendship entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemUserBundle:Friendship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Friendship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('friendship_edit', array('id' => $id)));
        }

        return $this->render('wizemUserBundle:Friendship:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Friendship entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('wizemUserBundle:Friendship')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Friendship entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('friendship'));
    }

    /**
     * Creates a form to delete a Friendship entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('friendship_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
