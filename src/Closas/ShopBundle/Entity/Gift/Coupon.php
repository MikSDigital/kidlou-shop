<?php

namespace Closas\ShopBundle\Entity\Gift;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Order;
use Closas\ShopBundle\Entity\Quote;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Gift\Coupon")
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
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Quote", inversedBy="coupons")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Order", inversedBy="coupons")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Gift", inversedBy="coupons")
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
     * @param \Closas\ShopBundle\Entity\Order $order
     *
     * @return Coupon
     */
    public function setOrder(\Closas\ShopBundle\Entity\Order $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Closas\ShopBundle\Entity\Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set quote
     *
     * @param \Closas\ShopBundle\Entity\Quote $quote
     *
     * @return Coupon
     */
    public function setQuote(\Closas\ShopBundle\Entity\Quote $quote = null) {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \Closas\ShopBundle\Entity\Quote
     */
    public function getQuote() {
        return $this->quote;
    }

    /**
     * Set gift
     *
     * @param \Closas\ShopBundle\Entity\Gift $gift
     *
     * @return Coupon
     */
    public function setGift(\Closas\ShopBundle\Entity\Gift $gift = null) {
        $this->gift = $gift;

        return $this;
    }

    /**
     * Get gift
     *
     * @return \Closas\ShopBundle\Entity\Gift
     */
    public function getGift() {
        return $this->gift;
    }

}
