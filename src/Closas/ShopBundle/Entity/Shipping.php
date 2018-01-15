<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shipping")
 */
class Shipping {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, options={"default":0})
     */
    protected $price;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, options={"default":0})
     */
    protected $price_limit;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Shipping
     */
    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return number
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param string $price_limit
     *
     * @return Shipping
     */
    public function setPriceLimit($price_limit) {
        $this->price_limit = $price_limit;
        return $this;
    }

    /**
     * Get price_limit
     *
     * @return number
     */
    public function getPriceLimit() {
        return $this->price_limit;
    }

}
