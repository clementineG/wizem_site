<?php

namespace wizem\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
*   @Security("has_role('ROLE_SUPER_ADMIN')")
*/
class AdminController extends Controller
{
    /**
    *   
    */
    public function indexAction()
    {

        $user = $this->getUser();
        $userWelcome = $user->getFirstname() ? ucfirst($user->getFirstname()) : $user->getUsername(); 

        $this->get('session')->getFlashBag()->add('info',"Bienvenue, $userWelcome !");

	  	return $this->render('wizemFrontBundle:Admin:index.html.twig');
    }

    /**
    *   Render log files
    */
    public function logAction()
    {
        $logFiles = array();
        $tabLogName = array();
        if ($handle = opendir($this->get('kernel')->getRootDir().'/logs')) {

            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, -3) == "log"){//} && substr($entry, 0, 4) == "prod"){
                    $logFiles[] = $entry;
                    $logName = explode("-",explode(".", $entry)[1])[0];
                    if($logName != "log" && !in_array($logName, $tabLogName)){
                        $tabLogName[] = $logName;
                    }
                }
            }
            closedir($handle);
        }

        asort($logFiles);
        
        return $this->render('wizemFrontBundle:Admin:log.html.twig', array(
            'logFiles' => $logFiles,
            'tabLogName' => $tabLogName,
        ));
    }

    /**
    *   Download $filename log
    */
    public function downloadLogFileAction($filename)
    {
        $path = $this->get('kernel')->getRootDir()."/logs/";

        $response = new Response();
        $response->setContent(file_get_contents($path . $filename));
        $response->headers->set(
           'Content-Type',
           'application/log'
        );
        $response->headers->set('Content-disposition', 'filename=' . $filename);

        return $response;
    }

}
