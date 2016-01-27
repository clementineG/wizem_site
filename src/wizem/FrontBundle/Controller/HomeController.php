<?php

namespace wizem\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
	/**
	*	Redirect user if connected or not
	*/
    public function indexAction()
    {
        if(!$this->container->get('security.context')->isGranted('ROLE_USER')){
	        return $this->render('wizemFrontBundle:Home:index.html.twig');
        }

	    return $this->render('wizemFrontBundle:Dashboard:index.html.twig');
    }

}
