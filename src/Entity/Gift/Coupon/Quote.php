<?php

namespace App\Entity\Gift\Coupon;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_coupon_quote")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Coupon\Quote")
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
    protected $created_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Quote", inversedBy="gift_coupon_quote")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    private $quote;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set quote
     *
     * @param \App\Entity\Quote $quote
     *
     * @return coupon_quote
     */
    public function setQuote(\App\Entity\Quote $quote = null) {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \App\Entity\Quote
     */
    public function getQuote() {
        return $this->quote;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $created_at
     *
     * @return coupon_quote
     */
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;

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

}
