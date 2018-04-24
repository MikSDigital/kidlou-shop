<?php

namespace App\Entity\Gift\Coupon;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon_counter")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Coupon\Quote")
 */
class Counter {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Quote", inversedBy="coupon_counter")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="coupon_counter")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gift\Coupon", inversedBy="coupon_counters")
     * @ORM\JoinColumn(name="coupon_id", referencedColumnName="id")
     */
    private $coupon;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $is_active;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set quote
     *
     * @param \App\Entity\Quote $quote
     *
     * @return coupon_quote
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
     * Set order
     *
     * @param \App\Entity\Order $order
     *
     * @return Order
     */
    public function setOrder(\App\Entity\Order $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \App\Entity\Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $created_at
     *
     * @return coupon_quote
     */
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Get is_active
     *
     * @return type $is_active
     */
    public function getIsActive() {
        return $this->is_active;
    }

    /**
     *
     * @param type $is_active
     * @return Quote
     */
    public function setIsActive($is_active) {
        $this->is_active = $is_active;

        return $this;
    }

}
