<?php

namespace wizem\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Place
 *
 * @ORM\Table(name="Place", indexes={@ORM\Index(name="fk_Place_Event1_idx", columns={"Event_id"})})
 * @ORM\Entity
 */
class Place
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
     * @ORM\Column(name="final", type="boolean", nullable=false, options={"default" = false})
     */
    private $final;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=45, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=45, nullable=true)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lng", type="string", length=45, nullable=true)
     */
    private $lng;

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
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="place")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $event;

    /**
     * @var \User
     *
     * @ORM\OneToOne(targetEntity="wizem\UserBundle\Entity\User", inversedBy="place")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="date", cascade={"persist","remove"})
     */
    private $vote;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vote = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Place
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
     * Set address
     *
     * @param string $address
     *
     * @return Place
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Place
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     *
     * @return Place
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set dateCreated
     *
     * @param string $dateCreated
     *
     * @return Place
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param string $dateUpdated
     *
     * @return Place
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return string
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
     * @return Place
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
     * Add vote
     *
     * @param \wizem\EventBundle\Entity\Vote $vote
     *
     * @return Place
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
     * Set final
     *
     * @param boolean $final
     *
     * @return Place
     */
    public function setFinal($final)
    {
        $this->final = $final;

        return $this;
    }

    /**
     * Get final
     *
     * @return boolean
     */
    public function getFinal()
    {
        return $this->final;
    }


    public function getCoords($adress)
    {
        $geocoder = "http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false";
        
        $adresse = $adress." France";

        $url_address = utf8_encode($adresse);
        $url_address = urlencode($url_address);
        $query = sprintf($geocoder,$url_address);
        $results = file_get_contents($query);

        $json = (json_decode($results));

        if($json->{'status'} == "OK"){
            $latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            //$ville = $json->{'results'}[0]->{'formatted_address'};
        }else{
            $latitude = null;
            $longitude = null;
        }

        return array("lat" => $latitude, "lng" => $longitude);
    }

    /**
     * Set user
     *
     * @param \wizem\UserBundle\Entity\User $user
     *
     * @return Place
     */
    public function setUser(\wizem\UserBundle\Entity\User $user = null)
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
