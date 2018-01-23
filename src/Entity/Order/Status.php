<?php

namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

// Die verschiedenen Statuse werden hier gespeichert, Pending, Processing, Complete, Closed, Canceled, On Hold
/**
 * @ORM\Entity
 * @ORM\Table(name="order_status")
 */
class Status {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $name = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="status")
     */
    private $orders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Status
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add order
     *
     * @param \App\Entity\Order $order
     *
     * @return Status
     */
    public function addOrder(\App\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \App\Entity\Order $order
     */
    public function removeOrder(\App\Entity\Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
