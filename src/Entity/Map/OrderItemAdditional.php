<?php

namespace App\Entity\Map;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_item_map_additional")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Map\OrderItemAdditional")
 */
class OrderItemAdditional {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent;

    /**
     * @ORM\Column(type="integer")
     */
    private $children;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent_product;

    /**
     * @ORM\Column(type="integer")
     */
    private $children_product;

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
     * Set parent product
     *
     * @param integer $parent_product
     *
     * @return OrderProductAdditional
     */
    public function setParentProduct($parent_product) {
        $this->parent_product = $parent_product;

        return $this;
    }

    /**
     * Get parent product
     *
     * @return integer
     */
    public function getParentProduct() {
        return $this->parent_product;
    }

    /**
     * Set children product
     *
     * @param integer $children_product
     *
     * @return OrderProductAdditional
     */
    public function setChildrenProduct($children_product) {
        $this->children_product = $children_product;

        return $this;
    }

    /**
     * Get children product
     *
     * @return integer
     */
    public function getChildrenProduct() {
        return $this->children_product;
    }

}
