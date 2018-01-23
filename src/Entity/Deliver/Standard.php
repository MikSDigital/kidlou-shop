<?php

namespace App\Entity\Deliver;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="deliver_standard")
 */
class Standard {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $monday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $tuesday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $wednesday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $thursday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $friday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $saturday;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $sunday;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set Monday
     *
     * @param boolean $monday
     *
     * @return Standard
     */
    public function setMonday($monday) {
        $this->monday = $monday;
        return $this;
    }

    /**
     * Get Monday
     *
     * @return boolean
     */
    public function getMonday() {
        return $this->monday;
    }

    /**
     * Set Tuesday
     *
     * @param boolean $tuesday
     *
     * @return Standard
     */
    public function setTuesday($tuesday) {
        $this->tuesday = $tuesday;
        return $this;
    }

    /**
     * Get Tuesday
     *
     * @return boolean
     */
    public function getTuesday() {
        return $this->tuesday;
    }

    /**
     * Set Wednesday
     *
     * @param boolean $wednesday
     *
     * @return Standard
     */
    public function setWednesday($wednesday) {
        $this->wednesday = $wednesday;
        return $this;
    }

    /**
     * Get Wednesday
     *
     * @return boolean
     */
    public function getWednesday() {
        return $this->wednesday;
    }

    /**
     * Set Thursday
     *
     * @param boolean $thursday
     *
     * @return Standard
     */
    public function setThursday($thursday) {
        $this->thursday = $thursday;
        return $this;
    }

    /**
     * Get Thursday
     *
     * @return boolean
     */
    public function getThursday() {
        return $this->thursday;
    }

    /**
     * Set Friday
     *
     * @param boolean $friday
     *
     * @return Standard
     */
    public function setFriday($friday) {
        $this->friday = $friday;
        return $this;
    }

    /**
     * Get Friday
     *
     * @return boolean
     */
    public function getFriday() {
        return $this->friday;
    }

    /**
     * Set Saturay
     *
     * @param boolean $saturday
     *
     * @return Standard
     */
    public function setSaturday($saturday) {
        $this->saturday = $saturday;
        return $this;
    }

    /**
     * Get Saturday
     *
     * @return boolean
     */
    public function getSaturday() {
        return $this->saturday;
    }

    /**
     * Set Sunday
     *
     * @param boolean $sunday
     *
     * @return Standard
     */
    public function setSunday($sunday) {
        $this->sunday = $sunday;
        return $this;
    }

    /**
     * Get Sunday
     *
     * @return boolean
     */
    public function getSunday() {
        return $this->sunday;
    }

}
