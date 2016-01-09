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
use wizem\EventBundle\Entity\Place;

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
     * Get an application formated User.
     *
     * @param User $user
     */
    public function getFormatedUser($user, $storage = false)
    {
        if($storage == true){
            return array(
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
            );
        }

        return array(
            "id" => $user->getId(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "birthDate" => $user->getBirthDate() ? $user->getBirthDate() : null,
            "adresse" => $user->getPlace() ? $user->getPlace()->getAddress() : null,
            "image" => $user->getImage(),
        );
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
            if($friend ->getFriend()->getId() != $user->getId()){
                $friends[] = array(
                    "id" => $friend ->getFriend()->getId(), 
                    "username" => $friend ->getFriend()->getUsername(),
                    "firstname" => $friend ->getFriend()->getFirstname(),
                    "lastname" => $friend ->getFriend()->getLastname(),
                );
            }
            if($friend ->getUser()->getId() != $user->getId()){
                $friends[] = array(
                    "id" => $friend ->getUser()->getId(), 
                    "username" => $friend ->getUser()->getUsername(),
                    "firstname" => $friend ->getUser()->getFirstname(),
                    "lastname" => $friend ->getUser()->getLastname(),
                );
            }
        }

        // Suppression des doublons
        //$friends = (array_unique($friends));
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

        if ( !preg_match("/^[a-zA-Z0-9_]{1,50}$/ " , $username ) ){
            unset($parameters['password']);
            $this->logger->error("User username not valid : ", array($parameters));
            $this->logger->info(" ===== New User from API ending ===== ");
            throw new InvalidUserException('User username not valid', $user);
        }

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

            return $this->getFormatedUser($user, true);
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
        if(isset($parameters['notification'])){
            // Updating notification
            $notification = $user->getNotification();
            $user->setNotification($parameters['notification']);
            $this->logger->info("Updating notification '{$notification}' to '{$parameters['notification']}' OK");
        }
        if(isset($parameters['birthDate'])){
            // Updating birthDate
            $birthDate = $user->getBirthDate() ? $user->getBirthDate()->format("Y-m-d H:i:s") : '';
            $dateObject = \Datetime::createFromFormat('Y-m-d H:i:s', $parameters['birthDate']." 00:00:00");  
            //return $dateObject;
            $user->setBirthDate($dateObject);
            $this->logger->info("Updating birthDate '{$birthDate}' to '{$parameters['birthDate']}' OK");
        }
        if(isset($parameters['place'])){
            // Updating place
            $place = $user->getPlace();
            if($place){
                // Updating existing place
                $oldAdress = $place->getAddress();

                $place->setAddress($parameters['place']);
                $coords = $place->getCoords($parameters['place']);
                $place->setLat($coords['lat']);
                $place->setLng($coords['lng']);
                
                $this->om->persist($place);

                $this->logger->info("Updating place '{$oldAdress}' to '{$parameters['place']}' OK");
            }else{
                // Creating new place
                $newPlace = new Place();
                $newPlace->setAddress($parameters['place']);
                $coords = $newPlace->getCoords($parameters['place']);
                $newPlace->setLat($coords['lat']);
                $newPlace->setLng($coords['lng']);
                $newPlace->setUser($user);
                $newPlace->setFinal(true);

                $user->setPlace($newPlace);
                $this->om->persist($newPlace);
                
                $this->logger->info("Creating new place : '{$parameters['place']}' OK");
            }
        }
        if(isset($parameters['image'])){
            // Updating image
            $image = $user->getImage();
            $user->setImage($parameters['image']);
            $this->logger->info("Updating image '{$image}' to '{$parameters['image']}' OK");
        }

        $this->om->persist($user);
        $this->om->flush();

        return $this->getFormatedUser($user);
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

        $this->logger->info("Deleting user #{$id} OK");

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
                $this->logger->info("User #{$user->getId()} has not friendship relation with friend #{$friend->getId()}");
                throw new AccessDeniedException('User has not friendship relation with friend');
            }
        }

        $this->om->remove($friendship);
        $this->om->flush();

        $this->logger->info("User #{$user->getId()} delete friend #{$friend->getId()} OK");

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

        $this->logger->info("User login : {$username}");

        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($username);
        if(!$user){
            $user = $um->findUserByEmail($username);
        }

        if(!$user instanceof User){
            $this->logger->info("User not found");
            throw new NotFoundHttpException("User not found");
        }
        if(!$this->checkUserPassword($user, $password)){
            $this->logger->info("Wrong password");
            throw new AccessDeniedException("Wrong password");
        }

        // Pas besoin de loger l'user, c'est géré dans le local storage du mobile.
        //$this->loginUser($user);

        $this->logger->info("Login OK");

        return $this->getFormatedUser($user, true);
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


    /**
     * Valid a vote (place or date) by the host of an event.
     *
     * @param User          $user
     * @param Event         $event
     * @param Array        $parameters
     *
     * @return Event
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function validVote(User $user, Event $event, array $parameters)
    {
        $this->container->get('wizem_api.event.handler')->checkIfUserLinkToEvent($event, $user, true);

        if(isset($parameters['date'])){
            // Validate date
            $date = $this->om->getRepository("wizemEventBundle:Date")->find($parameters['date']);
            if(!$date){
                $this->logger->info("Invalid id date");
                throw new InvalidFormException('Invalid Id date');
            }
            $date->setFinal(true);

            $this->om->persist($date);
            $this->om->flush();

            $this->logger->info("Validate date '#{$date->getId()}' OK");
        }
        if(isset($parameters['place'])){
            // Validate place
            $place = $this->om->getRepository("wizemEventBundle:Place")->find($parameters['place']);
            if(!$place){
                $this->logger->info("Invalid id place");
                throw new InvalidFormException('Invalid Id place');
            }
            $place->setFinal(true);

            $this->om->persist($place);
            $this->om->flush();
            
            $this->logger->info("Validate place '#{$place->getId()}' OK");
        }

        return $event->getId();
    }

    /**
     * Confirm presence or not in an event for an user
     *
     * @param User          $user
     * @param Event         $event
     * @param Array         $parameters
     *
     * @return 
     *
     * @throws wizem\ApiBundle\Exception\InvalidFormException
     */
    public function confirm(User $user, Event $event, array $parameters)
    {
        $this->container->get('wizem_api.event.handler')->checkIfUserLinkToEvent($event, $user, false);

        if(!isset($parameters['confirm'])){
            $this->logger->info("Invalid json");
            throw new InvalidFormException('Invalid json');
        }

        $confirm = ($parameters['confirm'] == 1) ? "true" : "false"; 
        
        // Validate confirmation
        $userEvent = $this->om->getRepository("wizemUserBundle:UserEvent")->findOneBy(array("event" => $event->getId(), "user" => $user->getId()));

        // A host can't confirm 
        if($userEvent->getHost() == true){
            $this->logger->info("User is the host, he can't confirm a presence or not");
            throw new InvalidFormException("You are the host, you can't confirm");
        }

        $userEvent->setState($parameters['confirm']);

        $this->om->persist($userEvent);
        $this->om->flush();

        $this->logger->info("Confirmation : {$confirm} OK");

        return $parameters['confirm'];
    }

}