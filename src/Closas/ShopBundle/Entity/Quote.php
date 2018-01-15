<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Calendar;
use Closas\ShopBundle\Entity\Map\QuoteProductAdditional;
use Closas\ShopBundle\Entity\Gift\Coupon;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Quote")
 */
class Quote {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Calendar", mappedBy="quote")
     */
    private $calendars;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Map\QuoteProductAdditional", mappedBy="quote")
     */
    private $productadditionals;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Gift\Coupon", mappedBy="quote")
     */
    private $coupons;

    public function __construct() {
        $this->calendars = new ArrayCollection();
        $this->productadditionals = new ArrayCollection();
        $this->coupons = new ArrayCollection();
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
     * Add calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     *
     * @return Quote
     */
    public function addCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars[] = $calendar;

        return $this;
    }

    /**
     * Remove calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars->removeElement($calendar);
    }

    /**
     * Get calendars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendars() {
        return $this->calendars;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Quote
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Quote
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * Add productadditional
     *
     * @param \Closas\ShopBundle\Entity\Map\QuoteProductAdditional $productadditional
     *
     * @return Quote
     */
    public function addProductadditional(\Closas\ShopBundle\Entity\Map\QuoteProductAdditional $productadditional) {
        $this->productadditionals[] = $productadditional;

        return $this;
    }

    /**
     * Remove productadditional
     *
     * @param \Closas\ShopBundle\Entity\Map\QuoteProductAdditional $productadditional
     */
    public function removeProductadditional(\Closas\ShopBundle\Entity\Map\QuoteProductAdditional $productadditional) {
        $this->productadditionals->removeElement($productadditional);
    }

    /**
     * Get productadditionals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductadditionals() {
        return $this->productadditionals;
    }

    /**
     * Add coupon
     *
     * @param \Closas\ShopBundle\Entity\Gift\Coupon $coupon
     *
     * @return Quote
     */
    public function addCoupon(\Closas\ShopBundle\Entity\Gift\Coupon $coupon) {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \Closas\ShopBundle\Entity\Gift\Coupon $coupon
     */
    public function removeCoupon(\Closas\ShopBundle\Entity\Gift\Coupon $coupon) {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupon
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons() {
        return $this->coupons;
    }

}
