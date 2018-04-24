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
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon\Quote", mappedBy="coupon")
     */
    private $gift_coupon_quotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon\Order", mappedBy="coupon")
     */
    private $gift_coupon_orders;

    public function __construct() {
        $this->gift_coupon_quotes = new ArrayCollection();
        $this->gift_coupon_orders = new ArrayCollection();
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
     * Add giftCouponQuote
     *
     * @param \App\Entity\Gift\Coupon\Quote $giftCouponQuote
     *
     * @return Coupon
     */
    public function addGiftCouponQuote(\App\Entity\Gift\Coupon\Quote $giftCouponQuote) {
        $this->gift_coupon_quotes[] = $giftCouponQuote;

        return $this;
    }

    /**
     * Remove giftCouponQuote
     *
     * @param \App\Entity\Gift\Coupon\Quote $giftCouponQuote
     */
    public function removeGiftCouponQuote(\App\Entity\Gift\Coupon\Quote $giftCouponQuote) {
        $this->gift_coupon_quotes->removeElement($giftCouponQuote);
    }

    /**
     * Get giftCouponQuotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGiftCouponQuote() {
        return $this->gift_coupon_quotes;
    }

    /**
     * Add giftCouponOrder
     *
     * @param \App\Entity\Gift\Coupon\Order $giftCouponOrder
     *
     * @return Coupon
     */
    public function addGiftCouponOrder(\App\Entity\Gift\Coupon\Order $giftCouponQuote) {
        $this->gift_coupon_orders[] = $giftCouponQuote;

        return $this;
    }

    /**
     * Remove giftCouponOrder
     *
     * @param \App\Entity\Gift\Coupon\Order $giftCouponOrder
     */
    public function removeGiftCouponOrder(\App\Entity\Gift\Coupon\Order $giftCouponQuote) {
        $this->gift_coupon_orders->removeElement($giftCouponQuote);
    }

    /**
     * Get giftCouponQuotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGiftCouponOrder() {
        return $this->gift_coupon_orders;
    }

}
