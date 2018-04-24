<?php

namespace App\Entity\Gift\Coupon;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon_order")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Coupon\Order")
 */
class Order {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="gift_coupon_order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

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
     * @return Order
     */
    public function setIsActive($is_active) {
        $this->is_active = $is_active;

        return $this;
    }

}
