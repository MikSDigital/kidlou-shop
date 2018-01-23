<?php

namespace App\Entity\Map;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote_product_map_additional")
 * @ORM\Entity(repositoryClass="App\Repository\Map\QuoteProductAdditional")
 */
class QuoteProductAdditional {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quote", inversedBy="productadditionals")
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
     * @param \App\Entity\Quote $quote
     *
     * @return QuoteProductAdditional
     */
    public function setQuote(\App\Entity\Quote $quote = null)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \App\Entity\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }
}
