<?php

namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_address")
 * @ORM\Entity(repositoryClass="App\Repository\Order\Address")
 */
class Address {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $email = NULL;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mobile = NULL;

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
     * @ORM\Column(type="string", length=100)
     */
    private $address_typ;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="addresses")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="addresses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Quote
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Quote
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Address
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Address
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Address
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Address
     */
    public function setPhone($phone) {
        $this->phone = $phone;

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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Address
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;

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
     * Set postCode
     *
     * @param string $postCode
     *
     * @return Address
     */
    public function setPostCode($postCode) {
        $this->post_code = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string
     */
    public function getPostCode() {
        return $this->post_code;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Address
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
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return Address
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
     * Set addressTyp
     *
     * @param string $addressTyp
     *
     * @return Address
     */
    public function setAddressTyp($addressTyp) {
        $this->address_typ = $addressTyp;

        return $this;
    }

    /**
     * Get addressTyp
     *
     * @return string
     */
    public function getAddressTyp() {
        return $this->address_typ;
    }

    /**
     * Set quote
     *
     * @param \App\Entity\Quote $quote
     *
     * @return Address
     */
    public function setQuote(\App\Entity\Quote $quote = null) {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \App\Entity\Quote
     */
    public function getQuote() {
        return $this->quote;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet($street) {
        $this->street = $street;

        return $this;
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
     * Set order
     *
     * @param \App\Entity\Order $order
     *
     * @return Address
     */
    public function setOrder(\App\Entity\Order $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \App\Entity\Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return Address
     */
    public function setUser(\App\Entity\User $user = null) {
        $this->user = $user;

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

}
