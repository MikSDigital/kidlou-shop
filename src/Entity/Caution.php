<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="caution")
 */
class Caution {

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
     * @return Caution
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

}
