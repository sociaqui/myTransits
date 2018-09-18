<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transit
 *
 * @ORM\Table(name="transit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransitRepository")
 */
class Transit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="string", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="distanceKilometers", type="decimal", precision=7, scale=2)
     */
    private $distanceKilometers;

    /**
     * @var int
     *
     * @ORM\Column(name="createdAt", type="integer", options={"unsigned":"true"})
     */
    private $createdAt;

    /**
     * @var array
     *
     * @ORM\Column(name="locations", type="array")
     */
    private $locations;


    /**
     * Transit constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        if(isset($data['distanceKilometers'])){
            $this->distanceKilometers = $data['distanceKilometers'];
        }
        if(isset($data['locations'])){
            $this->locations = $data['locations'];
        }
        $this->createdAt = time();
        return $this;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Transit
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set distance
     *
     * @param string $distance
     *
     * @return Transit
     */
    public function setDistance($distance)
    {
        $this->distanceKilometers = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->distanceKilometers;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Transit
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set locations
     *
     * @param array $locations
     *
     * @return Transit
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * Get locations
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }
}

