<?php

namespace wizem\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEvent
 *
 * @ORM\Table(name="User_Event", indexes={@ORM\Index(name="fk_User_Event_Event1_idx", columns={"Event_id"})})
 * @ORM\Entity
 */
class UserEvent
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
     * @var boolean
     *
     * @ORM\Column(name="host", type="boolean", nullable=true)
     */
    private $host;

    /**
     * @var boolean
     *
     * @ORM\Column(name="state", type="boolean", nullable=true)
     */
    private $state;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="wizem\EventBundle\Entity\Event", inversedBy="userEvent")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $event;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userEvent")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $user;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return UserEvent
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
     * Set host
     *
     * @param boolean $host
     *
     * @return UserEvent
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return boolean
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set state
     *
     * @param boolean $state
     *
     * @return UserEvent
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set event
     *
     * @param \wizem\EventBundle\Entity\Event $event
     *
     * @return UserEvent
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
     * Set user
     *
     * @param \wizem\UserBundle\Entity\User $user
     *
     * @return UserEvent
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
}
