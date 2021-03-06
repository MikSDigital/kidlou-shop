<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;

/**
 * @ORM\Entity
 * @ORM\Table(name="price")
 * @ORM\Entity(repositoryClass="App\Repository\Price")
 */
class Price {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $value;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Product", inversedBy="price")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set sale
     *
     * @param string $sale
     *
     * @return Price
     */
    public function setSale($sale) {
        $this->sale = $sale;
        return $this;
    }

    /**
     * Get sale
     *
     * @return string
     */
    public function getSale() {
        return $this->sale;
    }

    /**
     * Set product
     *
     * @param \App\Entity\Product $product
     *
     * @return Price
     */
    public function setProduct(\App\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \App\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Price
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

}
