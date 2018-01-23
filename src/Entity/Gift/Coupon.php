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
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Gift\Coupon")
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
     * @ORM\Column(type="integer")
     */
    private $counter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quote", inversedBy="coupons")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="coupons")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gift", inversedBy="coupons")
     * @ORM\JoinColumn(name="gift_id", referencedColumnName="id")
     */
    private $gift;

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
     * Set order
     *
     * @param \App\Entity\Order $order
     *
     * @return Coupon
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
     * Set quote
     *
     * @param \App\Entity\Quote $quote
     *
     * @return Coupon
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

}
