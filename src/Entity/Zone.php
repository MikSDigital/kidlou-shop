<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="zone")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Zone")
 */
class Zone {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $equatorial_radius;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $distance;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Zone
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Zone
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Set equatorial_radius
     *
     * @param string $equatorial_radius
     *
     * @return Zone
     */
    public function setEquatorialRadius($equatorial_radius) {
        $this->equatorial_radius = $equatorial_radius;
        return $this;
    }

    /**
     * Get equatorial_radius
     *
     * @return string
     */
    public function getEquatorialRadius() {
        return $this->equatorial_radius;
    }

    /**
     * Set distance
     *
     * @param string $distance
     *
     * @return Zone
     */
    public function setDistance($distance) {
        $this->distance = $distance;
        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance() {
        return $this->distance;
    }

}
