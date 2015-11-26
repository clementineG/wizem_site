<?php

namespace wizem\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Vote
 *
 * @ORM\Table(name="Vote", indexes={@ORM\Index(name="fk_Vote_User1_idx", columns={"User_id"}), @ORM\Index(name="fk_Vote_Date1_idx", columns={"Date_id"}), @ORM\Index(name="fk_Vote_Place1_idx", columns={"Place_id"}), @ORM\Index(name="fk_Vote_Event1_idx", columns={"Event_id"})})
 * @ORM\Entity
 */
class Vote
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
     * @var \User
     *
     * @ORM\OneToOne(targetEntity="wizem\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="User_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Date
     *
     * @ORM\OneToOne(targetEntity="Date")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Date_id", referencedColumnName="id")
     * })
     */
    private $date;

    /**
     * @var \Place
     *
     * @ORM\OneToOne(targetEntity="Place")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Place_id", referencedColumnName="id")
     * })
     */
    private $place;

    /**
     * @var \Event
     *
     * @ORM\OneToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Event_id", referencedColumnName="id")
     * })
     */
    private $event;

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
     * Set id
     *
     * @param integer $id
     *
     * @return Vote
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
     * Set user
     *
     * @param \wizem\UserBundle\Entity\User $user
     *
     * @return Vote
     */
    public function setUser(\wizem\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \wizem\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \wizem\EventBundle\Entity\Date $date
     *
     * @return Vote
     */
    public function setDate(\wizem\EventBundle\Entity\Date $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \wizem\EventBundle\Entity\Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set place
     *
     * @param \wizem\EventBundle\Entity\Place $place
     *
     * @return Vote
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
     * Set event
     *
     * @param \wizem\EventBundle\Entity\Event $event
     *
     * @return Vote
     */
    public function setEvent(\wizem\EventBundle\Entity\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \wizem\EventBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Vote
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
     * @return Vote
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
}
