<?php

namespace wizem\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('wizemFrontBundle:Home:index.html.twig');
    }
}
