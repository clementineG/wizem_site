<?php

namespace wizem\ApiBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use wizem\UserBundle\Entity\User;
use wizem\UserBundle\Entity\Friendship;
use wizem\EventBundle\Entity\Event;

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

    public function __construct(
        ObjectManager $om, $entityClass, 
        FormFactoryInterface $formFactory, 
        ContainerInterface $container, 
        $logger, 
        EncoderFactoryInterface $encoderFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->container = $container;
        $this->logger = $logger;
        $this->encoderFactory = $encoderFactory;
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
     * Get all User.
     *
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Get all friends of a User.
     *
     */
    public function getAllFriends($user)
    {
        $friendship = $this->om->getRepository("wizemUserBundle:Friendship")->getFriends($user->getId());

        $friends = array();
        foreach ($friendship as $friend) {
            if($friend ->getFriend()->getId() != $user->getId())
                $friends[] = $friend ->getFriend()->getId();
            if($friend ->getUser()->getId() != $user->getId())
                $friends[] = $friend ->getUser()->getId();
        }

        // Suppression des doublons
        $friends = (array_unique($friends));
        return $friends;
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

        // Check if user already exists in database
        $username = $parameters['username'];
        $email = $parameters['email'];

        // Check if username already exists
        $userCheck = $um->findUserByUsername($username);
        if($userCheck){
            unset($parameters['password']);
            $this->logger->error("User username already exists : ", array($parameters));
            $this->logger->info(" ===== New User from API ending ===== ");
            throw new InvalidUserException('User username already exists', $user);
        }

        // Check if email already exists
        $userCheck = $um->findUserByEmail($email);
        if($userCheck){
            unset($parameters['password']);
            $this->logger->error("User email already exists : ", array($parameters));
            $this->logger->info(" ===== New User from API ending ===== ");
            throw new InvalidUserException('User email already exists', $user);
        }

        return $this->createUserProcessForm($user, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param User      $user
     * @param array     $parameters
     * @param String    $method
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    private function createUserProcessForm(User $user, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new UserType(), $user, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        $this->logger->info("Processing form");
        if ($form->isValid()) {

            $data = $form->getData();
            $this->logger->info("Submit post form ok");

            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->createUser();

            $user->setEmail($data->getEmail());
            $user->setUsername($data->getUsername());
            $user->setPlainPassword($data->getPassword());
            $user->setEnabled(true);

            $userManager->updateUser($user);
            $this->logger->info("User created");

            return $user;
        }

        $this->logger->info("Invalid submitted data");
        $this->logger->info(" ===== New User from API ending ===== ");
        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Upadte an User.
     *
     * @param array $parameters
     *
     * @return User
     */
    public function update(array $parameters, $user)
    {

        return $this->updateUserProcessForm($user, $parameters, 'PUT');
    }

    /**
     * Processes the form.
     *
     * @param User          $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return User
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    private function updateUserProcessForm(User $user, array $parameters, $method = "PUT")
    {
        if(isset($parameters['firstname'])){
            // Updating firstname
            $firstname = $user->getFirstname();
            $user->setFirstname($parameters['firstname']);
            $this->logger->info("Updating firstname '{$firstname}' to '{$parameters['firstname']}' OK");
        }
        if(isset($parameters['lastname'])){
            // Updating lastname
            $lastname = $user->getLastname();
            $user->setLastname($parameters['lastname']);
            $this->logger->info("Updating lastname '{$lastname}' to '{$parameters['lastname']}' OK");
        }
        // if(isset($parameters['place'])){
        //     // Updating place
            
        //     $lastname = $user->getLastname();
        //     $user->setLastname($parameters['lastname']);
        //     $this->logger->info("Updating lastname '{$lastname}' to '{$parameters['lastname']}' OK");
        // }

        $this->om->persist($user);
        $this->om->flush();

        return $user;

        $form = $this->formFactory->create(new EventType($this->container, $method), $event, array('method' => $method));

        unset($parameters['userId']);


        $form->submit($parameters, 'PATCH' !== $method);



        return $event;

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Check all users for an event.
     *
     * @param User      $user
     * @param Event     $event
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function getAllUsersEvent(User $user, Event $event)
    {
        $this->container->get('wizem_api.event.handler')->checkIfUserLinkToEvent($event, $user, true);
        
        $friends = $this->getAllFriends($user);

        $friendsToInvite = array();
        foreach($friends as $friend) {
            $userEvent = $this->om->getRepository("wizemUserBundle:UserEvent")->findOneBy(array("event" => $event->getId(), "user" => $friend));
            if(!$userEvent){
                $friendsToInvite[] = $friend;
            }
        }
        
        return $friendsToInvite;
    }

    /**
     * Delete an User.
     *
     * @param mixed $id
     *
     * @return mixed $id
     */
    public function delete($id)
    {
        $user = $this->repository->find($id);

        $this->om->remove($user);
        $this->om->flush();

        return $id;
    }

    /**
     * Add a friend for this user
     *
     * @param mixed $id
     *
     * @return mixed $id
     */
    public function addFriend($user, $request)
    {
        $username = $request['username'];

        $this->logger->info("User trying to add '{$username}'");

        if($username != ""){
            
            $um = $this->container->get('fos_user.user_manager');
            $friend = $um->findUserByUsername($username);
            if(!$friend){
                $this->logger->error("Friend not found");
                throw new NotFoundHttpException("Friend not found");
            }
            
            // Test if user and friend are already friends
            $testFriendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $user->getId(), "friend" => $friend->getId()));
            if($testFriendship){
                $this->logger->error("Users are already friends");
                throw new AccessDeniedException('You are already friends');
            }
            $testFriendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $friend->getId(), "friend" => $user->getId()));
            if($testFriendship){
                $this->logger->error("Users are already friends");
                throw new AccessDeniedException('You are already friends');
            }

            $friendship = new Friendship(); 
            $friendship->setUser($user);
            $friendship->setFriend($friend);

            $this->om->persist($friendship);
            $this->om->flush();
                
            $this->logger->info("Friend added");

            return $friend;
        }
        
        throw new InvalidFormException('Invalid username');
    }

    /**
     * Delete an User.
     *
     * @param mixed $id
     *
     * @return mixed $id
     */
    public function deleteFriend($user, $friend)
    {
        // Test if user and friend are friends
        $friendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $user->getId(), "friend" => $friend->getId()));
        if(!$friendship){
            $friendship = $this->om->getRepository("wizemUserBundle:Friendship")->findOneBy(array("user" => $friend->getId(), "friend" => $user->getId()));
            if(!$friendship){
                throw new AccessDeniedException('User has not friendship relation with friend');
            }
        }

        $this->om->remove($friendship);
        $this->om->flush();

        return $friend->getId();
    }

    /**
     * Connect an user
     *
     * @param array $parameters
     *
     * @return User $user
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function connect(array $parameters)
    {
        $username = $parameters['username'];
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

        // Pas besoin de loger l'user, c'est géré dans le local storage du mobile.
        //$this->loginUser($user);

        return $user;
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
    // protected function loginUser(User $user)
    // {
    //     $security = $this->securityContext;
    //     $providerKey = $this->container->getParameter('fos_user.firewall_name');
    //     $roles = $user->getRoles();
    //     $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
    //     $security->setToken($token);
    // }

}