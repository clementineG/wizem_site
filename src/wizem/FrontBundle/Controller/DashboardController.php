<?php

namespace wizem\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
    *
    */
    public function indexAction()
    {
	    return $this->render('wizemFrontBundle:Dashboard:index.html.twig');
    
    }

}
