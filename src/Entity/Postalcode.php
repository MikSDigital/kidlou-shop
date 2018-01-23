<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="postalcode")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Postalcode")
 */
class Postalcode {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $country_code;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $plz;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $kanton;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $admin_code1;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $bezirk;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $admin_code2;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $admin_code3;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, options={"default":0})
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, options={"default":0})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $admin_code4;

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return Postalcode
     */
    public function setCountryCode($countryCode) {
        $this->country_code = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode() {
        return $this->country_code;
    }

    /**
     * Set plz
     *
     * @param integer $plz
     *
     * @return Postalcode
     */
    public function setPlz($plz) {
        $this->plz = $plz;

        return $this;
    }

    /**
     * Get plz
     *
     * @return integer
     */
    public function getPlz() {
        return $this->plz;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Postalcode
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set kanton
     *
     * @param string $kanton
     *
     * @return Postalcode
     */
    public function setKanton($kanton) {
        $this->kanton = $kanton;

        return $this;
    }

    /**
     * Get kanton
     *
     * @return string
     */
    public function getKanton() {
        return $this->kanton;
    }

    /**
     * Set adminCode1
     *
     * @param string $adminCode1
     *
     * @return Postalcode
     */
    public function setAdminCode1($adminCode1) {
        $this->admin_code1 = $adminCode1;

        return $this;
    }

    /**
     * Get adminCode1
     *
     * @return string
     */
    public function getAdminCode1() {
        return $this->admin_code1;
    }

    /**
     * Set bezirk
     *
     * @param string $bezirk
     *
     * @return Postalcode
     */
    public function setBezirk($bezirk) {
        $this->bezirk = $bezirk;

        return $this;
    }

    /**
     * Get bezirk
     *
     * @return string
     */
    public function getBezirk() {
        return $this->bezirk;
    }

    /**
     * Set adminCode2
     *
     * @param string $adminCode2
     *
     * @return Postalcode
     */
    public function setAdminCode2($adminCode2) {
        $this->admin_code2 = $adminCode2;

        return $this;
    }

    /**
     * Get adminCode2
     *
     * @return string
     */
    public function getAdminCode2() {
        return $this->admin_code2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Postalcode
     */
    public function setCity($city) {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set adminCode3
     *
     * @param string $adminCode3
     *
     * @return Postalcode
     */
    public function setAdminCode3($adminCode3) {
        $this->admin_code3 = $adminCode3;

        return $this;
    }

    /**
     * Get adminCode3
     *
     * @return string
     */
    public function getAdminCode3() {
        return $this->admin_code3;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Postalcode
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
     * @return Postalcode
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
     * Set adminCode4
     *
     * @param string $adminCode4
     *
     * @return Postalcode
     */
    public function setAdminCode4($adminCode4) {
        $this->admin_code4 = $adminCode4;

        return $this;
    }

    /**
     * Get adminCode4
     *
     * @return string
     */
    public function getAdminCode4() {
        return $this->admin_code4;
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
}
