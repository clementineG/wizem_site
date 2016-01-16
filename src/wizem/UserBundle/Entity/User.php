<?php

namespace wizem\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=45, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=45, nullable=true)
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    private $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notification", type="boolean", nullable=true, options={"default" = false})
     */
    private $notification;

    /**
     * @var text
     *
     * @ORM\Column(name="image", type="text", nullable=true)
     */
    private $image;

    /**
     * @var text
     *
     * @ORM\Column(name="facebookId", type="text", nullable=true)
     */
    private $facebookId;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @var \Place
     *
     * @ORM\OneToOne(targetEntity="wizem\EventBundle\Entity\Place", inversedBy="user", cascade={"persist","remove"})
     */
    private $place;

    /**
     * @var \Pro
     *
     * @ORM\OneToOne(targetEntity="Pro", inversedBy="user", cascade={"persist","remove"})
     */
    private $pro;

    /**
     * @ORM\OneToMany(targetEntity="wizem\EventBundle\Entity\Message", mappedBy="user", cascade={"persist"})
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="UserEvent", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userEvent;

    /**
     * @ORM\OneToMany(targetEntity="wizem\EventBundle\Entity\Vote", mappedBy="user", cascade={"persist"})
     */
    private $vote;

    /**
     * @ORM\OneToMany(targetEntity="wizem\EventBundle\Entity\Media", mappedBy="user", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="user", cascade={"persist", "remove"})
     */
    private $friendshipUser;

    /**
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="friend", cascade={"persist", "remove"})
     */
    private $friendshipFriend;

    public function __construct()
    {
        parent::__construct();
        $this->message = new \Doctrine\Common\Collections\ArrayCollection();
        $this->message = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userEvent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->friendshipUser = new \Doctrine\Common\Collections\ArrayCollection();
        $this->friendshipFriend = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notification = 0;
    }
    
    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     *
     * @return User
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set pro
     *
     * @param \wizem\UserBundle\Entity\Pro $pro
     *
     * @return User
     */
    public function setPro(\wizem\UserBundle\Entity\Pro $pro)
    {
        $this->pro = $pro;

        return $this;
    }

    /**
     * Get pro
     *
     * @return \wizem\UserBundle\Entity\Pro
     */
    public function getPro()
    {
        return $this->pro;
    }

    /**
     * Set place
     *
     * @param \wizem\EventBundle\Entity\Place $place
     *
     * @return User
     */
    public function setPlace(\wizem\EventBundle\Entity\Place $place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \wizem\EventBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
    
    /**
     * Add message
     *
     * @param \wizem\EventBundle\Entity\Message $message
     *
     * @return User
     */
    public function addMessage(\wizem\EventBundle\Entity\Message $message)
    {
        $this->message[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \wizem\EventBundle\Entity\Message $message
     */
    public function removeMessage(\wizem\EventBundle\Entity\Message $message)
    {
        $this->message->removeElement($message);
    }

    /**
     * Get message
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add vote
     *
     * @param \wizem\EventBundle\Entity\Vote $vote
     *
     * @return User
     */
    public function addVote(\wizem\EventBundle\Entity\Vote $vote)
    {
        $this->vote[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \wizem\EventBundle\Entity\Vote $vote
     */
    public function removeVote(\wizem\EventBundle\Entity\Vote $vote)
    {
        $this->vote->removeElement($vote);
    }

    /**
     * Get vote
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Add media
     *
     * @param \wizem\EventBundle\Entity\Media $media
     *
     * @return User
     */
    public function addMedia(\wizem\EventBundle\Entity\Media $media)
    {
        $this->media[] = $media;

        return $this;
    }

    /**
     * Remove media
     *
     * @param \wizem\EventBundle\Entity\Media $media
     */
    public function removeMedia(\wizem\EventBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set notification
     *
     * @param boolean $notification
     *
     * @return User
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return boolean
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Add friendshipUser
     *
     * @param \wizem\UserBundle\Entity\Friendship $friendshipUser
     *
     * @return User
     */
    public function addFriendshipUser(\wizem\UserBundle\Entity\Friendship $friendshipUser)
    {
        $this->friendshipUser[] = $friendshipUser;

        return $this;
    }

    /**
     * Remove friendshipUser
     *
     * @param \wizem\UserBundle\Entity\Friendship $friendshipUser
     */
    public function removeFriendshipUser(\wizem\UserBundle\Entity\Friendship $friendshipUser)
    {
        $this->friendshipUser->removeElement($friendshipUser);
    }

    /**
     * Get friendshipUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriendshipUser()
    {
        return $this->friendshipUser;
    }

    /**
     * Add friendshipFriend
     *
     * @param \wizem\UserBundle\Entity\Friendship $friendshipFriend
     *
     * @return User
     */
    public function addFriendshipFriend(\wizem\UserBundle\Entity\Friendship $friendshipFriend)
    {
        $this->friendshipFriend[] = $friendshipFriend;

        return $this;
    }

    /**
     * Remove friendshipFriend
     *
     * @param \wizem\UserBundle\Entity\Friendship $friendshipFriend
     */
    public function removeFriendshipFriend(\wizem\UserBundle\Entity\Friendship $friendshipFriend)
    {
        $this->friendshipFriend->removeElement($friendshipFriend);
    }

    /**
     * Get friendshipFriend
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriendshipFriend()
    {
        return $this->friendshipFriend;
    }

    /**
     * Add userEvent
     *
     * @param \wizem\UserBundle\Entity\UserEvent $userEvent
     *
     * @return User
     */
    public function addUserEvent(\wizem\UserBundle\Entity\UserEvent $userEvent)
    {
        $this->userEvent[] = $userEvent;

        return $this;
    }

    /**
     * Remove userEvent
     *
     * @param \wizem\UserBundle\Entity\UserEvent $userEvent
     */
    public function removeUserEvent(\wizem\UserBundle\Entity\UserEvent $userEvent)
    {
        $this->userEvent->removeElement($userEvent);
    }

    /**
     * Get userEvent
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserEvent()
    {
        return $this->userEvent;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }
}
