<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Util\Codes;

use wizem\ApiBundle\Form\DiscussionType;

use wizem\EventBundle\Entity\Event;
use wizem\EventBundle\Entity\Discussion;

use wizem\UserBundle\Entity\User;

class DiscussionHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $container;
    private $logger;

    public function __construct(
        ObjectManager $om, 
        $entityClass, 
        FormFactoryInterface $formFactory, 
        ContainerInterface $container, 
        $logger)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Create a new Discusison.
     *
     * @param Event  $event
     *
     * @return Discussion
     */
    public function create(Event $event)
    {
        $this->logger->info(" = New Discussion from API begin =");
        $discussion = new $this->entityClass();

        $parameters['event'] = $event->getId();

        // Process form does all the magic, validate and hydrate the event object.
        return $this->createDiscussionProcessForm($discussion, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param Discussion    $discussion
     * @param array         $parameters
     * @param String        $method
     *
     * @return Discussion
     *
     * @throws Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function createDiscussionProcessForm(Discussion $discussion, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new DiscussionType(), $discussion, array('method' => $method));

        $form->submit($parameters, 'PATCH' !== $method);
        $this->logger->info("Processing form");
        
        if ($form->isValid()) {

            $discussion = $form->getData();
            $this->om->persist($discussion);
            $this->om->flush();

            $this->logger->info(" = New Discussion from API ending = ");
            return $discussion;
        }

        $this->logger->info("Invalid discussion submitted data");
        $this->logger->info(" = New Discussion from API ending = ");
        throw new HttpException(Codes::HTTP_BAD_REQUEST, "Invalid discussion submitted data");
    }

}