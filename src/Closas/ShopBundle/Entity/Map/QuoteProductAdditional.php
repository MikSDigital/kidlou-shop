<?php

namespace Closas\ShopBundle\Entity\Map;

use Closas\ShopBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote_product_map_additional")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Map\QuoteProductAdditional")
 */
class QuoteProductAdditional {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Quote", inversedBy="productadditionals")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

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
     * @return QuoteProductAdditional
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
     * @return QuoteProductAdditional
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
     * Set quote
     *
     * @param \Closas\ShopBundle\Entity\Quote $quote
     *
     * @return QuoteProductAdditional
     */
    public function setQuote(\Closas\ShopBundle\Entity\Quote $quote = null)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \Closas\ShopBundle\Entity\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }
}
