<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

use wizem\UserBundle\Form\UserType;
use wizem\UserBundle\Entity\User;

use wizem\ApiBundle\Exception\InvalidFormException;

class UserHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $logger;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $logger)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->logger = $logger;
    }

    /**
     * Get an User.
     *
     * @param mixed $id
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get an User.
     *
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Create a new User.
     *
     * @param array $parameters
     */
    public function post(array $parameters)
    {
        $user = new $this->entityClass();

        // Process form does all the magic, validate and hydrate the User object.
        return $this->processForm($user, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param userInterface $user
     * @param array         $parameters
     * @param String        $method
     *
     * @throws \ApiBundle\Exception\InvalidFormException
     */
    private function processForm(User $user, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new UserType(), $user, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $user = $form->getData();
            $this->om->persist($user);
            $this->om->flush($user);

            return $user;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Delete an User.
     *
     * @param mixed $id
     *
     * @return ItemInterface
     */
    public function delete($id)
    {
        $user = $this->repository->find($id);

        $this->om->remove($user);
        $this->om->flush();

        return $id;
    }


}