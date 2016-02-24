<?php

namespace wizem\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use wizem\EventBundle\Entity\Typeevent;
use wizem\EventBundle\Form\TypeeventType;

/**
 *  Typeevent controller.
 *  @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class TypeeventController extends Controller
{

    /**
     * Lists all Typeevent entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('wizemEventBundle:Typeevent')->findAll();

        return $this->render('wizemEventBundle:Typeevent:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Typeevent entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Typeevent();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wizem_event_type_show', array('id' => $entity->getId())));
        }

        return $this->render('wizemEventBundle:Typeevent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Typeevent entity.
     *
     * @param Typeevent $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Typeevent $entity)
    {
        $form = $this->createForm(new TypeeventType(), $entity, array(
            'action' => $this->generateUrl('wizem_event_type_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Typeevent entity.
     *
     */
    public function newAction()
    {
        $entity = new Typeevent();
        $form   = $this->createCreateForm($entity);

        return $this->render('wizemEventBundle:Typeevent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Typeevent entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemEventBundle:Typeevent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Typeevent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('wizemEventBundle:Typeevent:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Typeevent entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemEventBundle:Typeevent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Typeevent entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('wizemEventBundle:Typeevent:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Typeevent entity.
    *
    * @param Typeevent $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Typeevent $entity)
    {
        $form = $this->createForm(new TypeeventType(), $entity, array(
            'action' => $this->generateUrl('wizem_event_type_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Typeevent entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('wizemEventBundle:Typeevent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Typeevent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wizem_event_type_edit', array('id' => $id)));
        }

        return $this->render('wizemEventBundle:Typeevent:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Typeevent entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('wizemEventBundle:Typeevent')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Typeevent entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('wizem_event_type'));
    }

    /**
     * Creates a form to delete a Typeevent entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wizem_event_type_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
