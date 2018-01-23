<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Calendar;
use App\Entity\Map\OrderProductAdditional;
use App\Entity\Gift\Coupon;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Order")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Calendar", mappedBy="order")
     */
    private $calendars;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Map\OrderProductAdditional", mappedBy="order")
     */
    private $productadditionals;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Quote")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order\Status", inversedBy="orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order\Address", mappedBy="order")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon", mappedBy="order")
     */
    private $coupons;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="orders")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order\Payment", mappedBy="order")
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
     * @param \App\Entity\Calendar $calendar
     *
     * @return Order
     */
    public function addCalendar(\App\Entity\Calendar $calendar) {
        $this->calendars[] = $calendar;

        return $this;
    }

    /**
     * Remove calendar
     *
     * @param \App\Entity\Calendar $calendar
     */
    public function removeCalendar(\App\Entity\Calendar $calendar) {
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
     * @param \App\Entity\Map\OrderProductAdditional $productadditional
     *
     * @return Order
     */
    public function addProductadditional(\App\Entity\Map\OrderProductAdditional $productadditional) {
        $this->productadditionals[] = $productadditional;

        return $this;
    }

    /**
     * Remove productadditional
     *
     * @param \App\Entity\Map\OrderProductAdditional $productadditional
     */
    public function removeProductadditional(\App\Entity\Map\OrderProductAdditional $productadditional) {
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
     * @param \App\Entity\Quote $quote
     *
     * @return Order
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
     * Set status
     *
     * @param \App\Entity\Order\Status $status
     *
     * @return Order
     */
    public function setStatus(\App\Entity\Order\Status $status = null) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \App\Entity\Order\Status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add address
     *
     * @param \App\Entity\Order\Address $address
     *
     * @return Order
     */
    public function addAddress(\App\Entity\Order\Address $address) {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \App\Entity\Order\Address $address
     */
    public function removeAddress(\App\Entity\Order\Address $address) {
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
     * @param \App\Entity\Language $lang
     *
     * @return Order
     */
    public function setLang(\App\Entity\Language $lang = null) {
        $this->lang = $lang;

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
     * Set payment
     *
     * @param \App\Entity\Order\Payment $payment
     *
     * @return Order
     */
    public function setPayment(\App\Entity\Order\Payment $payment = null) {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \App\Entity\Order\Payment
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Add coupon
     *
     * @param \App\Entity\Gift\Coupon $coupon
     *
     * @return Quote
     */
    public function addCoupon(\App\Entity\Gift\Coupon $coupon) {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \App\Entity\Gift\Coupon $coupon
     */
    public function removeCoupon(\App\Entity\Gift\Coupon $coupon) {
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
