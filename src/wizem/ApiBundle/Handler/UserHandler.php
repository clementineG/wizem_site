<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

use wizem\UserBundle\Entity\User;

use wizem\ApiBundle\Form\UserType;
use wizem\ApiBundle\Exception\InvalidFormException;
use wizem\ApiBundle\Exception\InvalidUserException;

class UserHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $container;
    private $logger;
    private $encoderFactory;
    private $securityContext;

    public function __construct(
        ObjectManager $om, $entityClass, 
        FormFactoryInterface $formFactory, 
        ContainerInterface $container, 
        $logger, 
        EncoderFactoryInterface $encoderFactory,
        SecurityContext $securityContext)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->container = $container;
        $this->logger = $logger;
        $this->encoderFactory = $encoderFactory;
        $this->securityContext = $securityContext;
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
    public function create(array $parameters)
    {
        $user = new $this->entityClass();

        $um = $this->container->get('fos_user.user_manager');

        $username = $parameters['email'];
        $userCheck = $um->findUserByUsername($username);
        if(!$userCheck){
            $userCheck = $um->findUserByEmail($username);
        }
        if($userCheck){
            throw new InvalidUserException('User already exists', $user);
        }

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

            $data = $form->getData();

            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->createUser();

            $user->setEmail($data->getEmail());
            $user->setUsername($data->getEmail());
            $user->setPlainPassword($data->getPassword());
            $user->setEnabled(true);

            $userManager->updateUser($user);

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

    /**
     * Create a new User.
     *
     * @param array $parameters
     */
    public function connect(array $parameters)
    {
        $username = $parameters['email'];
        $password = $parameters['password'];

        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($username);
        if(!$user){
            $user = $um->findUserByEmail($username);
        }

        if(!$user instanceof User){
            throw new NotFoundHttpException("User not found");
        }
        if(!$this->checkUserPassword($user, $password)){
            throw new AccessDeniedException("Wrong password");
        }

        $this->loginUser($user);

        return array('success'=>true, 'user'=>$user);
    }

    /**
    *   Check the password user with Symfony security 
    */
    protected function checkUserPassword(User $user, $password)
    {
        $factory = $this->encoderFactory;
        $encoder = $factory->getEncoder($user);
        if(!$encoder){
            return false;
        }
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }

    /**
    *   Log the user 
    */
    protected function loginUser(User $user)
    {
        $security = $this->securityContext;
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $security->setToken($token);
    }

}