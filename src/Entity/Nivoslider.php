<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="nivoslider")
 * @ORM\Entity(repositoryClass="App\Repository\Nivoslider")
 */
class Nivoslider {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $status = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Nivoslider\Item", mappedBy="nivoslider")
     */
    private $items;

    /**
     * Constructor
     */
    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add item
     *
     * @param \App\Entity\Nivoslider\Item $item
     *
     * @return Nivoslider
     */
    public function addItem(\App\Entity\Nivoslider\Item $item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \App\Entity\Nivoslider\Item $item
     */
    public function removeItem(\App\Entity\Nivoslider\Item $item) {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems() {
        return $this->items;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Nivoslider
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}
