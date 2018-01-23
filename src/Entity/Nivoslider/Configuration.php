<?php

namespace App\Entity\Nivoslider;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="nivoslider_configuration")
 * @ORM\Entity(repositoryClass="App\Repository\Nivoslider\Configuration")
 */
class Configuration {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $status = null;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $animation = null;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $speed = null;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $qty_item = null;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $description = null;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $next_back = null;

    /**
     * @ORM\Column(type="smallint", length=6)
     */
    private $nav_ctrl = null;

    /**
     * @ORM\Column(type="string", name="`interval`", length=255)
     */
    private $interval;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Nivoslider
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set animation
     *
     * @param string $animation
     *
     * @return Nivoslider
     */
    public function setAnimation($animation) {
        $this->animation = $animation;

        return $this;
    }

    /**
     * Get animation
     *
     * @return string
     */
    public function getAnimation() {
        return $this->animation;
    }

    /**
     * Set speed
     *
     * @param string $speed
     *
     * @return Nivoslider
     */
    public function setSpeed($speed) {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return string
     */
    public function getSpeed() {
        return $this->speed;
    }

    /**
     * Set interval
     *
     * @param string $interval
     *
     * @return Nivoslider
     */
    public function setInterval($interval) {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get interval
     *
     * @return string
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * Set qtyItem
     *
     * @param string $qtyItem
     *
     * @return Nivoslider
     */
    public function setQtyItem($qtyItem) {
        $this->qty_item = $qtyItem;

        return $this;
    }

    /**
     * Get qtyItem
     *
     * @return string
     */
    public function getQtyItem() {
        return $this->qty_item;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Nivoslider
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return integer
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set nextBack
     *
     * @param integer $nextBack
     *
     * @return Nivoslider
     */
    public function setNextBack($nextBack) {
        $this->next_back = $nextBack;

        return $this;
    }

    /**
     * Get nextBack
     *
     * @return integer
     */
    public function getNextBack() {
        return $this->next_back;
    }

    /**
     * Set navCtrl
     *
     * @param integer $navCtrl
     *
     * @return Nivoslider
     */
    public function setNavCtrl($navCtrl) {
        $this->nav_ctrl = $navCtrl;

        return $this;
    }

    /**
     * Get navCtrl
     *
     * @return integer
     */
    public function getNavCtrl() {
        return $this->nav_ctrl;
    }

}
