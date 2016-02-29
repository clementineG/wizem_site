<?php

namespace wizem\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
    *   Redirect user if connected or not
    */
    public function indexAction()
    {
        if(!$this->container->get('security.context')->isGranted('ROLE_USER')){
            return $this->render('wizemFrontBundle:Home:index.html.twig');
        }

        if($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->redirectToRoute('wizem_front_admin_index');
        }

        $user = $this->getUser();
        $userWelcome = $user->getFirstname() ? ucfirst($user->getFirstname()) : $user->getUsername(); 
        $em = $this->getDoctrine()->getManager();

        $this->get('session')->getFlashBag()->add('info',"Bienvenue, $userWelcome !");

        $events = $em->getRepository('wizemUserBundle:UserEvent')->findByUser($this->getUser()->getId());


        $friendship = $em->getRepository("wizemUserBundle:Friendship")->getSiteFriends($user->getId());

        $friends = array();
        foreach ($friendship as $friend) {
            if($friend->getFriend()->getId() != $user->getId()){
                $friends[] = $friend;
            }
            if($friend->getUser()->getId() != $user->getId()){
                $friends[] = $friend;
            }
        }

        return $this->render('wizemFrontBundle:Dashboard:index.html.twig', array(
            'events' => $events,
            'friends' => $friends,
        ));
    }
	/**
	*	
	*/
    public function mentionsAction()
    {
	  	return $this->render('wizemFrontBundle:Home:mentions.html.twig');
    }

}
