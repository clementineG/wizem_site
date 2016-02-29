<?php

namespace wizem\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use wizem\EventBundle\Entity\Event;
use wizem\EventBundle\Form\EventType;
use wizem\EventBundle\Entity\Date;
use wizem\EventBundle\Entity\Place;
use wizem\EventBundle\Entity\Vote;
use wizem\EventBundle\Entity\Discussion;

use wizem\UserBundle\Entity\UserEvent;

/**
 * Event controller.
 * @Security("has_role('ROLE_USER')")
 *
 */
class EventController extends Controller
{

    /**
     * Lists all Event entities.
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('wizemUserBundle:UserEvent')->findByUser($this->getUser()->getId());
        //$event = $em->getRepository('wizemEventBundle:Event')->findByUser($this->getUser()->getId());
       // $events = $em->getRepository('wizemEventBundle:Event')->findAll();

        return $this->render('wizemEventBundle:Event:index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Displays a form to create a new Event entity.
     *
     */
    public function newAction(Request $request)
    {
        $event = new Event();

        $form = $this->createForm(new EventType(), $event, array(
            'method' => 'POST',
            'action' => $this->generateUrl('wizem_event_event_new'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // User_Event creation for for link between user and event
            $userEvent = new UserEvent(); 
            $userEvent->setEvent($event);
            $userEvent->setUser($this->getUser());
            // If creation of event, user is automatically the host and he participate 
            $userEvent->setState(1);
            $userEvent->setHost(1);

            foreach ($event->getPlace() as $place) {
                $final = count($event->getPlace()) > 1 ? false : true;
                $place->setFinal($final);
                $place->setEvent($event);
                $coords = $place->getCoords($place->getAddress());
                $place->setLat($coords['lat']);
                $place->setLng($coords['lng']);
            }

            foreach ($event->getDate() as $date) {
                $final = count($event->getDate()) > 1 ? false : true;
                $date->setFinal($final);
                $date->setEvent($event);
            }

            $em->persist($event);
            $em->persist($userEvent);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',"Votre évènement a bien été créé !");
            return $this->redirect($this->generateUrl('wizem_event_event_show', array('id' => $event->getId())));
        }

        return $this->render('wizemEventBundle:Event:new.html.twig', array(
            'event' => $event,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('wizemEventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event event.');
        }

        $finalPlace = $em->getRepository('wizemEventBundle:Place')->findOneBy(array("event" => $event->getId(), "final" => 1));
        $finalLat = $finalPlace ? $event->getPlace()[0]->getLat() : null;
        $finalLng = $finalPlace ? $event->getPlace()[0]->getLng() : null;

        $user = $this->getUser();
        $friendship = $em->getRepository("wizemUserBundle:Friendship")->getSiteFriends($user->getId());

        $friends = array();
        foreach ($friendship as $friend) {
            if($friend->getState() == 1){
                if($friend->getFriend()->getId() != $user->getId()){
                    $friends[] = $friend;
                }
                if($friend->getUser()->getId() != $user->getId()){
                    $friends[] = $friend;
                }
            }
        }

        return $this->render('wizemEventBundle:Event:show.html.twig', array(
            'event'         => $event,
            'finalLat'      => $finalLat,
            'finalLng'      => $finalLng,
            'friends'       => $friends,
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createForm(new EventType(), $entity, array(
            'method' => 'PUT',
        ));
        $editForm->handleRequest($request);

        $deleteForm = $this->createDeleteForm($id);

     //   $form->add('submit', 'submit', array('label' => 'Update'));
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wizem_event_event_show', array('id' => $id)));
        }

        return $this->render('wizemEventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('wizemEventBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('wizem_event_event'));
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wizem_event_event_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer'))
            ->getForm()
        ;
    }
}
