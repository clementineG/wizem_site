<?php

namespace wizem\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Event
 *
 * @ORM\Table(name="Event", indexes={@ORM\Index(name="fk_Event_TypeEvent1_idx", columns={"TypeEvent_id"})})
 * @ORM\Entity
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @var \Typeevent
     *
     * @ORM\ManyToOne(targetEntity="Typeevent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TypeEvent_id", referencedColumnName="id")
     * })
     */
    private $typeevent;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="event", cascade={"persist", "remove"})
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="Place", mappedBy="event", cascade={"persist", "remove"})
     */
    private $place;

    /**
     * @ORM\OneToMany(targetEntity="wizem\UserBundle\Entity\UserEvent", mappedBy="event", cascade={"persist", "remove"})
     */
    private $userEvent;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="event", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="event", cascade={"persist", "remove"})
     */
    private $shoppingItem;

    /**
     * @ORM\OneToMany(targetEntity="wizem\EventBundle\Entity\Vote", mappedBy="event", cascade={"persist"})
     */
    private $vote;

    /**
     * @ORM\OneToOne(targetEntity="wizem\EventBundle\Entity\Discussion", inversedBy="event", cascade={"persist","remove"})
     */
    private $discussion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \Doctrine\Common\Collections\ArrayCollection();
        $this->place = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userEvent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shoppingItem = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vote = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Event
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Event
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
     * @return Event
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
     * Set typeevent
     *
     * @param \wizem\EventBundle\Entity\Typeevent $typeevent
     *
     * @return Event
     */
    public function setTypeevent(\wizem\EventBundle\Entity\Typeevent $typeevent)
    {
        $this->typeevent = $typeevent;

        return $this;
    }

    /**
     * Get typeevent
     *
     * @return \wizem\EventBundle\Entity\Typeevent
     */
    public function getTypeevent()
    {
        return $this->typeevent;
    }

    /**
     * Add date
     *
     * @param \wizem\EventBundle\Entity\Date $date
     *
     * @return Event
     */
    public function addDate(\wizem\EventBundle\Entity\Date $date)
    {
        $this->date[] = $date;

        return $this;
    }

    /**
     * Remove date
     *
     * @param \wizem\EventBundle\Entity\Date $date
     */
    public function removeDate(\wizem\EventBundle\Entity\Date $date)
    {
        $this->date->removeElement($date);
    }

    /**
     * Get date
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add place
     *
     * @param \wizem\EventBundle\Entity\Place $place
     *
     * @return Event
     */
    public function addPlace(\wizem\EventBundle\Entity\Place $place)
    {
        $this->place[] = $place;

        return $this;
    }

    /**
     * Remove place
     *
     * @param \wizem\EventBundle\Entity\Place $place
     */
    public function removePlace(\wizem\EventBundle\Entity\Place $place)
    {
        $this->place->removeElement($place);
    }

    /**
     * Get place
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Add media
     *
     * @param \wizem\EventBundle\Entity\Media $media
     *
     * @return Event
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
     * Add shoppingItem
     *
     * @param \wizem\EventBundle\Entity\Media $shoppingItem
     *
     * @return Event
     */
    public function addShoppingItem(\wizem\EventBundle\Entity\Media $shoppingItem)
    {
        $this->shoppingItem[] = $shoppingItem;

        return $this;
    }

    /**
     * Remove shoppingItem
     *
     * @param \wizem\EventBundle\Entity\Media $shoppingItem
     */
    public function removeShoppingItem(\wizem\EventBundle\Entity\Media $shoppingItem)
    {
        $this->shoppingItem->removeElement($shoppingItem);
    }

    /**
     * Get shoppingItem
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShoppingItem()
    {
        return $this->shoppingItem;
    }

    /**
     * Add vote
     *
     * @param \wizem\EventBundle\Entity\Vote $vote
     *
     * @return Event
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
     * Add userEvent
     *
     * @param \wizem\UserBundle\Entity\UserEvent $userEvent
     *
     * @return Event
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
     * Set discussion
     *
     * @param \wizem\EventBundle\Entity\Discussion $discussion
     *
     * @return Event
     */
    public function setDiscussion(\wizem\EventBundle\Entity\Discussion $discussion = null)
    {
        $this->discussion = $discussion;

        return $this;
    }

    /**
     * Get discussion
     *
     * @return \wizem\EventBundle\Entity\Discussion
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }
}
