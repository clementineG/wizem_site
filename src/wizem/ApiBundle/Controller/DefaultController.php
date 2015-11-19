<?php

namespace wizem\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use wizem\EventBundle\Entity\Event;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('wizemApiBundle:Default:index.html.twig', array('name' => $entities));
    }
}
