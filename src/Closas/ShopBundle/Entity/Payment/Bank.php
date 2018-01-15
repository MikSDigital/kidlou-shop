<?php

namespace Closas\ShopBundle\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_bank")
 */
class Bank {

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
    private $account_holder = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $account_number = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $sort_code = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $bank_name = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $iban = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $bic = null;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment", inversedBy="banks")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Lang\Label", mappedBy="bank")
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
     * @return Bank
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
     * Set accountHolder
     *
     * @param string $accountHolder
     *
     * @return Bank
     */
    public function setAccountHolder($accountHolder) {
        $this->account_holder = $accountHolder;

        return $this;
    }

    /**
     * Get accountHolder
     *
     * @return string
     */
    public function getAccountHolder() {
        return $this->account_holder;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     *
     * @return Bank
     */
    public function setAccountNumber($accountNumber) {
        $this->account_number = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber() {
        return $this->account_number;
    }

    /**
     * Set sortCode
     *
     * @param string $sortCode
     *
     * @return Bank
     */
    public function setSortCode($sortCode) {
        $this->sort_code = $sortCode;

        return $this;
    }

    /**
     * Get sortCode
     *
     * @return string
     */
    public function getSortCode() {
        return $this->sort_code;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     *
     * @return Bank
     */
    public function setBankName($bankName) {
        $this->bank_name = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName() {
        return $this->bank_name;
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return Bank
     */
    public function setIban($iban) {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban() {
        return $this->iban;
    }

    /**
     * Set bic
     *
     * @param string $bic
     *
     * @return Bank
     */
    public function setBic($bic) {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic() {
        return $this->bic;
    }

    /**
     * Set payment
     *
     * @param \Closas\ShopBundle\Entity\Payment $payment
     *
     * @return Bank
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
     * @return Bank
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
     * @return Bank
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
