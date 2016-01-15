<?php

namespace wizem\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Discussion
 *
 * @ORM\Table(name="Discussion")
 * @ORM\Entity
 */
class Discussion
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
     * @var \Event
     *
     * @ORM\OneToOne(targetEntity="wizem\EventBundle\Entity\Event", mappedBy="discussion")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="discussion", cascade={"persist", "remove"})
     */
    private $message;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->message = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Discussion
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Discussion
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
     * @return Discussion
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
     * Set event
     *
     * @param \wizem\EventBundle\Entity\Event $event
     *
     * @return Discussion
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
     * Add message
     *
     * @param \wizem\EventBundle\Entity\Message $message
     *
     * @return Discussion
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
}
