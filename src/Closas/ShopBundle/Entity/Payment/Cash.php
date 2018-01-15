<?php

namespace Closas\ShopBundle\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_cash")
 */
class Cash {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status = false;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $formular_url;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $price = null;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment", inversedBy="cashs")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Lang\Label", mappedBy="cash")
     */
    private $labels;

    /**
     * Constructor
     */
    public function __construct() {
        $this->labels = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Cash
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Cash
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set payment
     *
     * @param \Closas\ShopBundle\Entity\Payment $payment
     *
     * @return Cash
     */
    public function setPayment(\Closas\ShopBundle\Entity\Payment $payment = null) {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Closas\ShopBundle\Entity\Payment
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Add label
     *
     * @param \Closas\ShopBundle\Entity\Payment\Lang\Label $label
     *
     * @return Cash
     */
    public function addLabel(\Closas\ShopBundle\Entity\Payment\Lang\Label $label) {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Remove label
     *
     * @param \Closas\ShopBundle\Entity\Payment\Lang\Label $label
     */
    public function removeLabel(\Closas\ShopBundle\Entity\Payment\Lang\Label $label) {
        $this->labels->removeElement($label);
    }

    /**
     * Get labels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLabels() {
        return $this->labels;
    }


    /**
     * Set formularUrl
     *
     * @param string $formularUrl
     *
     * @return Cash
     */
    public function setFormularUrl($formularUrl)
    {
        $this->formular_url = $formularUrl;

        return $this;
    }

    /**
     * Get formularUrl
     *
     * @return string
     */
    public function getFormularUrl()
    {
        return $this->formular_url;
    }
}
