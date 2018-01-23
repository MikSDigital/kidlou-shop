<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_personal")
 * @ORM\Entity(repositoryClass="App\Repository\User\Personal")
 */
class Personal {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25)
     */
    private $first_name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $post_code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $country_code;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone = NULL;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mobile = NULL;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $standard = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="personals")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="personals")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName) {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName) {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Personal
     */
    public function setStreet($street) {
        $this->street = $street;

        return $this;
    }

    /**
     * Get post_code
     *
     * @return string
     */
    public function getPostCode() {
        return $this->post_code;
    }

    /**
     * Set street
     *
     * @param string $post_code
     *
     * @return Personal
     */
    public function setPostCode($post_code) {
        $this->post_code = $post_code;

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
     * Set city
     *
     * @param string $city
     *
     * @return Personal
     */
    public function setCity($city) {
        $this->city = $city;

        return $this;
    }

    /**
     * Get country_code
     *
     * @return string
     */
    public function getCountryCode() {
        return $this->country_code;
    }

    /**
     * Set country_code
     *
     * @param string $country_code
     *
     * @return Personal
     */
    public function setCountryCode($contry_code) {
        $this->country_code = $contry_code;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Personal
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Personal
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStandard() {
        return $this->standard;
    }

    /**
     * Set status
     *
     * @param boolean $standard
     *
     * @return Personal
     */
    public function setStandard($standard) {
        $this->standard = $standard;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \App\Entity\Language
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Set lang
     *
     * @param \App\Entity\Language $lang
     *
     * @return Content
     */
    public function setLang(\App\Entity\Language $lang = null) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return Content
     */
    public function setUser(\App\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

}
