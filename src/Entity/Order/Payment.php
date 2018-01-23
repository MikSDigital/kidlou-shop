<?php

namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

// Die verschiedenen Statuse werden hier gespeichert, Pending, Processing, Complete, Closed, Canceled, On Hold
/**
 * @ORM\Entity
 * @ORM\Table(name="order_payment")
 * @ORM\Entity(repositoryClass="App\Repository\Order\Payment")
 */
class Payment {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $payment_name = null;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $shipping_cost;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $caution_cost = null;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $cash_cost = null;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $subtotal_cost = null;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $amount_subtotal_cost;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $amount_shipping_cost;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $additional_information = NULL;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $payment_additional_information = NULL;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="payment")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set shippingCost
     *
     * @param string $shippingCost
     *
     * @return Payment
     */
    public function setShippingCost($shippingCost) {
        $this->shipping_cost = $shippingCost;

        return $this;
    }

    /**
     * Get shippingCost
     *
     * @return string
     */
    public function getShippingCost() {
        return $this->shipping_cost;
    }

    /**
     * Set cautionCost
     *
     * @param string $cautionCost
     *
     * @return Payment
     */
    public function setCautionCost($cautionCost) {
        $this->caution_cost = $cautionCost;

        return $this;
    }

    /**
     * Get cautionCost
     *
     * @return string
     */
    public function getCautionCost() {
        return $this->caution_cost;
    }

    /**
     * Set cashCost
     *
     * @param string $cashCost
     *
     * @return Payment
     */
    public function setCashCost($cashCost) {
        $this->cash_cost = $cashCost;

        return $this;
    }

    /**
     * Get cashCost
     *
     * @return string
     */
    public function getCashCost() {
        return $this->cash_cost;
    }

    /**
     * Set subtotalCost
     *
     * @param string $subtotalCost
     *
     * @return Payment
     */
    public function setSubtotalCost($subtotalCost) {
        $this->subtotal_cost = $subtotalCost;

        return $this;
    }

    /**
     * Get subtotalCost
     *
     * @return string
     */
    public function getSubtotalCost() {
        return $this->subtotal_cost;
    }

    /**
     * Set amountSubtotalCost
     *
     * @param string $amountSubtotalCost
     *
     * @return Payment
     */
    public function setAmountSubtotalCost($amountSubtotalCost) {
        $this->amount_subtotal_cost = $amountSubtotalCost;

        return $this;
    }

    /**
     * Get amountSubtotalCost
     *
     * @return string
     */
    public function getAmountSubtotalCost() {
        return $this->amount_subtotal_cost;
    }

    /**
     * Set amountShippingCost
     *
     * @param string $amountShippingCost
     *
     * @return Payment
     */
    public function setAmountShippingCost($amountShippingCost) {
        $this->amount_shipping_cost = $amountShippingCost;

        return $this;
    }

    /**
     * Get amountShippingCost
     *
     * @return string
     */
    public function getAmountShippingCost() {
        return $this->amount_shipping_cost;
    }

    /**
     * Set additionalInformation
     *
     * @param string $additionalInformation
     *
     * @return Payment
     */
    public function setAdditionalInformation($additionalInformation) {
        $this->additional_information = $additionalInformation;

        return $this;
    }

    /**
     * Get additionalInformation
     *
     * @return string
     */
    public function getAdditionalInformation() {
        return $this->additional_information;
    }

    /**
     * Set paymentAdditionalInformation
     *
     * @param string $paymentAdditionalInformation
     *
     * @return Payment
     */
    public function setPaymentAdditionalInformation($paymentAdditionalInformation) {
        $this->payment_additional_information = $paymentAdditionalInformation;

        return $this;
    }

    /**
     * Get paymentAdditionalInformation
     *
     * @return string
     */
    public function getPaymentAdditionalInformation() {
        return $this->payment_additional_information;
    }

    /**
     * Set order
     *
     * @param \App\Entity\Order $order
     *
     * @return Payment
     */
    public function setOrder(\App\Entity\Order $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \App\Entity\Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set paymentName
     *
     * @param string $paymentName
     *
     * @return Payment
     */
    public function setPaymentName($paymentName) {
        $this->payment_name = $paymentName;

        return $this;
    }

    /**
     * Get paymentName
     *
     * @return string
     */
    public function getPaymentName() {
        return $this->payment_name;
    }

}
