<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Payment")
 */
class Payment {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $name = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $short_name = null;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status = false;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Bank", mappedBy="payment")
     */
    private $banks;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Cash", mappedBy="payment")
     */
    private $cashs;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Paypal", mappedBy="payment")
     */
    private $paypals;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Post", mappedBy="payment")
     */
    private $posts;

    /**
     * Constructor
     */
    public function __construct() {
        $this->banks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cashs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paypals = new \Doctrine\Common\Collections\ArrayCollection();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Payment
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Payment
     */
    public function setShortName($shortName) {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName() {
        return $this->short_name;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Payment
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add bank
     *
     * @param \Closas\ShopBundle\Entity\Payment\Bank $bank
     *
     * @return Payment
     */
    public function addBank(\Closas\ShopBundle\Entity\Payment\Bank $bank) {
        $this->banks[] = $bank;

        return $this;
    }

    /**
     * Remove bank
     *
     * @param \Closas\ShopBundle\Entity\Payment\Bank $bank
     */
    public function removeBank(\Closas\ShopBundle\Entity\Payment\Bank $bank) {
        $this->banks->removeElement($bank);
    }

    /**
     * Get banks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanks() {
        return $this->banks;
    }

    /**
     * Add cash
     *
     * @param \Closas\ShopBundle\Entity\Payment\Cash $cash
     *
     * @return Payment
     */
    public function addCash(\Closas\ShopBundle\Entity\Payment\Cash $cash) {
        $this->cashs[] = $cash;

        return $this;
    }

    /**
     * Remove cash
     *
     * @param \Closas\ShopBundle\Entity\Payment\Cash $cash
     */
    public function removeCash(\Closas\ShopBundle\Entity\Payment\Cash $cash) {
        $this->cashs->removeElement($cash);
    }

    /**
     * Get cashs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCashs() {
        return $this->cashs;
    }

    /**
     * Add paypal
     *
     * @param \Closas\ShopBundle\Entity\Payment\Paypal $paypal
     *
     * @return Payment
     */
    public function addPaypal(\Closas\ShopBundle\Entity\Payment\Paypal $paypal) {
        $this->paypals[] = $paypal;

        return $this;
    }

    /**
     * Remove paypal
     *
     * @param \Closas\ShopBundle\Entity\Payment\Paypal $paypal
     */
    public function removePaypal(\Closas\ShopBundle\Entity\Payment\Paypal $paypal) {
        $this->paypals->removeElement($paypal);
    }

    /**
     * Get paypals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaypals() {
        return $this->paypals;
    }

    /**
     * Add post
     *
     * @param \Closas\ShopBundle\Entity\Payment\Post $post
     *
     * @return Payment
     */
    public function addPost(\Closas\ShopBundle\Entity\Payment\Post $post) {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \Closas\ShopBundle\Entity\Payment\Post $post
     */
    public function removePost(\Closas\ShopBundle\Entity\Payment\Post $post) {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts() {
        return $this->posts;
    }

}
