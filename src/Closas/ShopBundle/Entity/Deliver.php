<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="deliver")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Deliver")
 */
class Deliver {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", name="`date`")
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean", options={"default":TRUE})
     */
    private $is_deliver = TRUE;

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
     * @param date $date
     *
     * @return Deliver
     */
    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set is_deliver
     *
     * @param boolean $deliver
     *
     * @return Deliver
     */
    public function setIsDeliver($deliver) {
        $this->is_deliver = $deliver;
        return $this;
    }

    /**
     * Get deliver
     *
     * @return date
     */
    public function getIsDeliver() {
        return $this->is_deliver;
    }

}
