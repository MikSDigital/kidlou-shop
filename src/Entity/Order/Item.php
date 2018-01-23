<?php

namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

// Die verschiedenen Statuse werden hier gespeichert, Pending, Processing, Complete, Closed, Canceled, On Hold
/**
 * @ORM\Entity
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Order\Item")
 */
class Item {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $sku;

    /**
     * @ORM\Column(type="integer", length=10, options={"default":0})
     */
    private $product_id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $name = null;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="status")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Calendar", inversedBy="order_items")
     * @ORM\JoinColumn(name="calendar_id", referencedColumnName="id")
     */
    private $calendar;

    /**
     * Constructor
     */
    public function __construct() {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Item
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
     * Set price
     *
     * @param string $price
     *
     * @return Item
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Add order
     *
     * @param \App\Entity\Order $order
     *
     * @return Item
     */
    public function addOrder(\App\Entity\Order $order) {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \App\Entity\Order $order
     */
    public function removeOrder(\App\Entity\Order $order) {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders() {
        return $this->orders;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Item
     */
    public function setSku($sku) {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * Set product_id
     *
     * @param integer $product_id
     *
     * @return Item
     */
    public function setProductId($product_id) {
        $this->product_id = $product_id;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer
     */
    public function getProductId() {
        return $this->product_id;
    }

    /**
     * Set calendar
     *
     * @param \App\Entity\Calendar $calendar
     *
     * @return Item
     */
    public function setCalendar(\App\Entity\Calendar $calendar = null) {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Get calendar
     *
     * @return \App\Entity\Calendar
     */
    public function getCalendar() {
        return $this->calendar;
    }

}
