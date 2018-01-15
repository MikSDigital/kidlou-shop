<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Calendar;
use Closas\ShopBundle\Entity\Map\OrderProductAdditional;
use Closas\ShopBundle\Entity\Gift\Coupon;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Order")
 */
class Order {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $order_number;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Calendar", mappedBy="order")
     */
    private $calendars;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Map\OrderProductAdditional", mappedBy="order")
     */
    private $productadditionals;

    /**
     * @ORM\OneToOne(targetEntity="Closas\ShopBundle\Entity\Quote")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Order\Status", inversedBy="orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Order\Address", mappedBy="order")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Gift\Coupon", mappedBy="order")
     */
    private $coupons;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Language", inversedBy="orders")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\OneToOne(targetEntity="Closas\ShopBundle\Entity\Order\Payment", mappedBy="order")
     */
    private $payment;

    /**
     *
     */
    public function __construct() {
        $this->calendars = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->productadditionals = new ArrayCollection();
        $this->coupons = new ArrayCollection();
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
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return Order
     */
    public function setOrderNumber($orderNumber) {
        $this->order_number = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * Add calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     *
     * @return Order
     */
    public function addCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars[] = $calendar;

        return $this;
    }

    /**
     * Remove calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars->removeElement($calendar);
    }

    /**
     * Get calendars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendars() {
        return $this->calendars;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Order
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
     * Add productadditional
     *
     * @param \Closas\ShopBundle\Entity\Map\OrderProductAdditional $productadditional
     *
     * @return Order
     */
    public function addProductadditional(\Closas\ShopBundle\Entity\Map\OrderProductAdditional $productadditional) {
        $this->productadditionals[] = $productadditional;

        return $this;
    }

    /**
     * Remove productadditional
     *
     * @param \Closas\ShopBundle\Entity\Map\OrderProductAdditional $productadditional
     */
    public function removeProductadditional(\Closas\ShopBundle\Entity\Map\OrderProductAdditional $productadditional) {
        $this->productadditionals->removeElement($productadditional);
    }

    /**
     * Get productadditionals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductadditionals() {
        return $this->productadditionals;
    }

    /**
     * Set quote
     *
     * @param \Closas\ShopBundle\Entity\Quote $quote
     *
     * @return Order
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
     * Set status
     *
     * @param \Closas\ShopBundle\Entity\Order\Status $status
     *
     * @return Order
     */
    public function setStatus(\Closas\ShopBundle\Entity\Order\Status $status = null) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Closas\ShopBundle\Entity\Order\Status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add address
     *
     * @param \Closas\ShopBundle\Entity\Order\Address $address
     *
     * @return Order
     */
    public function addAddress(\Closas\ShopBundle\Entity\Order\Address $address) {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \Closas\ShopBundle\Entity\Order\Address $address
     */
    public function removeAddress(\Closas\ShopBundle\Entity\Order\Address $address) {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses() {
        return $this->addresses;
    }

    /**
     * Set lang
     *
     * @param \Closas\ShopBundle\Entity\Language $lang
     *
     * @return Order
     */
    public function setLang(\Closas\ShopBundle\Entity\Language $lang = null) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \Closas\ShopBundle\Entity\Language
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Set payment
     *
     * @param \Closas\ShopBundle\Entity\Order\Payment $payment
     *
     * @return Order
     */
    public function setPayment(\Closas\ShopBundle\Entity\Order\Payment $payment = null) {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Closas\ShopBundle\Entity\Order\Payment
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Add coupon
     *
     * @param \Closas\ShopBundle\Entity\Gift\Coupon $coupon
     *
     * @return Quote
     */
    public function addCoupon(\Closas\ShopBundle\Entity\Gift\Coupon $coupon) {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \Closas\ShopBundle\Entity\Gift\Coupon $coupon
     */
    public function removeCoupon(\Closas\ShopBundle\Entity\Gift\Coupon $coupon) {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupon
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons() {
        return $this->coupons;
    }

}
