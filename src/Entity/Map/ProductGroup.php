<?php

namespace App\Entity\Map;

use App\Entity\Product\Group;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_map_group")
 */
class ProductGroup {

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\Group", inversedBy="product_groups")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;


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
     * @return ProductGroup
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
     * @return ProductGroup
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

    /**
     * Set group
     *
     * @param \App\Entity\Product\Group $group
     *
     * @return ProductGroup
     */
    public function setGroup(\App\Entity\Product\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\Entity\Product\Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
