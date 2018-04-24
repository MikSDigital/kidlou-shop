<?php

namespace App\Entity\Gift;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Order;
use App\Entity\Quote;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Coupon")
 */
class Coupon {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gift", inversedBy="coupons")
     * @ORM\JoinColumn(name="gift_id", referencedColumnName="id")
     */
    private $gift;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon\Counter", mappedBy="coupon")
     */
    private $coupon_counters;

    public function __construct() {
        $this->coupon_counters = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return Coupon
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set counter
     *
     * @param string $counter
     *
     * @return Coupon
     */
    public function setCounter($counter) {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter() {
        return $this->counter;
    }

    /**
     * Set gift
     *
     * @param \App\Entity\Gift $gift
     *
     * @return Coupon
     */
    public function setGift(\App\Entity\Gift $gift = null) {
        $this->gift = $gift;

        return $this;
    }

    /**
     * Get gift
     *
     * @return \App\Entity\Gift
     */
    public function getGift() {
        return $this->gift;
    }

    /**
     * Add couponCounter
     *
     * @param \App\Entity\Gift\Coupon\Counter $couponCounter
     *
     * @return Coupon
     */
    public function addCouponCounters(\App\Entity\Gift\Coupon\Counter $couponCounter) {
        $this->coupon_counters[] = $couponCounter;

        return $this;
    }

    /**
     * Remove couponCounter
     *
     * @param \App\Entity\Gift\Coupon\Counter $couponCounter
     */
    public function removeCouponCounters(\App\Entity\Gift\Coupon\Counter $couponCounter) {
        $this->coupon_counters->removeElement($couponCounter);
    }

    /**
     * Get couponCounters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCouponCounters() {
        return $this->coupon_counters;
    }

}
