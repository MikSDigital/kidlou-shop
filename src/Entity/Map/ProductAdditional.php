<?php

namespace App\Entity\Map;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_map_additional")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Map\ProductAdditional")
 */
class ProductAdditional {

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent
     *
     * @param integer $parent
     *
     * @return ProductAdditional
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param integer $children
     *
     * @return ProductAdditional
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer
     */
    public function getChildren()
    {
        return $this->children;
    }
}
