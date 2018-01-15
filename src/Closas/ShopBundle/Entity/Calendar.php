<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Order;
use Closas\ShopBundle\Entity\Quote;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Calendar")
 */
class Calendar {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_from;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_to;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Product", inversedBy="calendars")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Order", inversedBy="calendars")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Quote", inversedBy="calendars")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Order\Item", mappedBy="calendar")
     */
    private $order_items;

    /**
     * Constructor
     */
    public function __construct() {
        $this->order_items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set productId
     *
     * @param integer $productId
     *
     * @return Calendar
     */
    public function setProductId($productId) {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId() {
        return $this->product_id;
    }

    /**
     * Set order
     *
     * @param \Closas\ShopBundle\Entity\Order $order
     *
     * @return Calendar
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
     * @return Calendar
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return Calendar
     */
    public function setDateFrom($dateFrom) {
        $this->date_from = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom() {
        return $this->date_from;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return Calendar
     */
    public function setDateTo($dateTo) {
        $this->date_to = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo() {
        return $this->date_to;
    }

    /**
     * Set product
     *
     * @param \Closas\ShopBundle\Entity\Product $product
     *
     * @return Calendar
     */
    public function setProduct(\Closas\ShopBundle\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Closas\ShopBundle\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Add orderItem
     *
     * @param \Closas\ShopBundle\Entity\Order\Item $orderItem
     *
     * @return Calendar
     */
    public function addOrderItem(\Closas\ShopBundle\Entity\Order\Item $orderItem) {
        $this->order_items[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem
     *
     * @param \Closas\ShopBundle\Entity\Order\Item $orderItem
     */
    public function removeOrderItem(\Closas\ShopBundle\Entity\Order\Item $orderItem) {
        $this->order_items->removeElement($orderItem);
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems() {
        return $this->order_items;
    }

}
