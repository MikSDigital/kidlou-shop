<?php

namespace Closas\ShopBundle\Entity\Product;

use Closas\ShopBundle\Entity\Map\ProductGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_group")
 */
class Group {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Map\ProductGroup", mappedBy="group")
     */
    private $product_groups;

    public function __construct() {
        $this->$product_groups = new ArrayCollection();
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
     * @return Group
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
     * Add productGroup
     *
     * @param \Closas\ShopBundle\Entity\Map\ProductGroup $productGroup
     *
     * @return Group
     */
    public function addProductGroup(\Closas\ShopBundle\Entity\Map\ProductGroup $productGroup)
    {
        $this->product_groups[] = $productGroup;

        return $this;
    }

    /**
     * Remove productGroup
     *
     * @param \Closas\ShopBundle\Entity\Map\ProductGroup $productGroup
     */
    public function removeProductGroup(\Closas\ShopBundle\Entity\Map\ProductGroup $productGroup)
    {
        $this->product_groups->removeElement($productGroup);
    }

    /**
     * Get productGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductGroups()
    {
        return $this->product_groups;
    }
}
