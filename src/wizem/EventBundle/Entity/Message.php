<?php

namespace wizem\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Message
 *
 * @ORM\Table(name="Message")
 * @ORM\Entity
 */
class Message
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
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=45, nullable=true)
     */
    private $content;

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
     * @var \Discussion
     *
     * @ORM\ManyToOne(targetEntity="Discussion", inversedBy="message")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $discussion;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="wizem\UserBundle\Entity\User", inversedBy="message")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Message
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
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Message
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
     * @return Message
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
     * Set discussion
     *
     * @param \wizem\EventBundle\Entity\Discussion $discussion
     *
     * @return Message
     */
    public function setDiscussion(\wizem\EventBundle\Entity\Discussion $discussion)
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

    /**
     * Set user
     *
     * @param \wizem\UserBundle\Entity\User $user
     *
     * @return Message
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
