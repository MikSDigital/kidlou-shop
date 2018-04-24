<?php

namespace App\Entity\Gift;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Order;
use App\Entity\Quote;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Coupon")
 */
class Coupon {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gift", inversedBy="coupons")
     * @ORM\JoinColumn(name="gift_id", referencedColumnName="id")
     */
    private $gift;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift\Coupon\Counter", mappedBy="coupon")
     */
    private $counters;

    public function __construct() {
        $this->coupon_counters = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return Coupon
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set gift
     *
     * @param \App\Entity\Gift $gift
     *
     * @return Coupon
     */
    public function setGift(\App\Entity\Gift $gift = null) {
        $this->gift = $gift;

        return $this;
    }

    /**
     * Get gift
     *
     * @return \App\Entity\Gift
     */
    public function getGift() {
        return $this->gift;
    }

    /**
     * Add counter
     *
     * @param \App\Entity\Gift\Coupon\Counter $counter
     *
     * @return Coupon
     */
    public function addCouponCounters(\App\Entity\Gift\Coupon\Counter $counter) {
        $this->counters[] = $counter;

        return $this;
    }

    /**
     * Remove counter
     *
     * @param \App\Entity\Gift\Coupon\Counter $counter
     */
    public function removeCouponCounters(\App\Entity\Gift\Coupon\Counter $counter) {
        $this->counters->removeElement($counter);
    }

    /**
     * Get couponCounters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCounters() {
        return $this->counters;
    }

}
