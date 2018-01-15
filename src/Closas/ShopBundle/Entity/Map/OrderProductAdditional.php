<?php

namespace Closas\ShopBundle\Entity\Map;

use Closas\ShopBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_product_map_additional")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Map\OrderProductAdditional")
 */
class OrderProductAdditional {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Order", inversedBy="productadditionals")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent;

    /**
     * @ORM\Column(type="integer")
     */
    private $children;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set parent
     *
     * @param integer $parent
     *
     * @return OrderProductAdditional
     */
    public function setParent($parent) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param integer $children
     *
     * @return OrderProductAdditional
     */
    public function setChildren($children) {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Set order
     *
     * @param \Closas\ShopBundle\Entity\Order $order
     *
     * @return OrderProductAdditional
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

}
