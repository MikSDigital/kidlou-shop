<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Gift\Text;
use App\Entity\Gift\Coupon;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift")
 * @ORM\Entity(repositoryClass="App\Repository\Gift")
 */
class Gift {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $name;

    /**
     *
     * @ORM\Column(type="integer", length=6)
     */
    private $number_codes;

    /**
     *
     * @ORM\Column(type="integer", length=6)
     */
    private $length_code;

    /**
     *
     * @ORM\Column(type="integer", length=6)
     */
    private $percent;

    /**
     * @ORM\Column(name="is_active", type="boolean", options={"default":false})
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_from;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_to;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_uses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon", mappedBy="gift")
     */
    private $coupons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Text", mappedBy="gift")
     */
    private $texts;

    /**
     * Constructor
     */
    public function __construct() {
        $this->coupons = new ArrayCollection();
        $this->texts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Description
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set number codes
     *
     * @param string $number_codes
     *
     * @return Gift
     */
    public function setNumberCodes($number_codes) {
        $this->number_codes = $number_codes;

        return $this;
    }

    /**
     * Get number codes
     *
     * @return integer
     */
    public function getNumberCodes() {
        return $this->number_codes;
    }

    /**
     * Set length code
     *
     * @param string $length_code
     *
     * @return Gift
     */
    public function setLengthCode($length_code) {
        $this->length_code = $length_code;

        return $this;
    }

    /**
     * Get length code
     *
     * @return integer
     */
    public function getLengthCode() {
        return $this->length_code;
    }

    /**
     * Set percent
     *
     * @param string $percent
     *
     * @return Gift
     */
    public function setPercent($percent) {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return integer
     */
    public function getPercent() {
        return $this->percent;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Text
     */
    public function setText(\App\Entity\Gift\Text $text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Gift
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Gift
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set dateFrom
     *
     * @param string $dateFrom
     *
     * @return Gift
     */
    public function setDateFrom($dateFrom) {
        $this->date_from = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return datetime
     */
    public function getDateFrom() {
        return $this->date_from;
    }

    /**
     * Set dateTo
     *
     * @param string $dateTo
     *
     * @return Gift
     */
    public function setDateTo($dateTo) {
        $this->date_to = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return datetime
     */
    public function getDateTo() {
        return $this->date_to;
    }

    /**
     * Set maxUses
     *
     * @param string $maxUses
     *
     * @return Gift
     */
    public function setMaxUses($maxUses) {
        $this->max_uses = $maxUses;

        return $this;
    }

    /**
     * Get maxUses
     *
     * @return integer
     */
    public function getMaxUses() {
        return $this->max_uses;
    }

    /**
     * Add coupon
     *
     * @param \App\Entity\Gift\Coupon $coupon
     *
     * @return Gift
     */
    public function addCoupon(\App\Entity\Gift\Coupon $coupon) {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \App\Entity\Gift\Coupon $coupon
     */
    public function removeCoupon(\App\Entity\Gift\Coupon $coupon) {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons() {
        return $this->coupons;
    }

    /**
     * Add text
     *
     * @param \App\Entity\Gift\Text $text
     *
     * @return Gift
     */
    public function addText(\App\Entity\Gift\Text $text) {
        $this->texts[] = $text;

        return $this;
    }

    /**
     * Remove text
     *
     * @param \App\Entity\Gift\Text $text
     */
    public function removeText(\App\Entity\Gift\Text $text) {
        $this->texts->removeElement($text);
    }

    /**
     * Get texts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTexts() {
        return $this->texts;
    }

}
