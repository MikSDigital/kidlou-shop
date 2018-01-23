<?php

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Entity\Payment\Bank;
use App\Entity\Payment\Cash;
use App\Entity\Payment\Paypal;
use App\Entity\Payment\Post;
use App\Entity\Payment\Lang\Label;
use App\Service\Payment\Field;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Common;

class Typ {

    /**
     *
     * @var type $payment
     */
    private $payment;

    /**
     *
     * @var type $label;
     */
    private $label;

    /**
     *
     * @var type $bank
     */
    private $bank;

    /**
     *
     * @var type $cash
     */
    private $cash;

    /**
     *
     * @var type $paypal;
     */
    private $paypal;

    /**
     *
     * @var type $post;
     */
    private $post;

    /**
     *
     * @var type $payment_data_content
     */
    private $payment_data;

    /**
     *
     * @var type $common
     */
    private $common;

    /**
     *
     * @var type boolean
     */
    private $isTable;

    /**
     *
     * @param Payment $payment
     * @param Label $label
     * @param Bank $bank
     * @param Cash $cash
     * @param Paypal $paypal
     * @param Post $post
     * @param Common $common;
     */
    public function __construct(Payment $payment, Label $label = NULL, Bank $bank = NULL, Cash $cash = NULL, Paypal $paypal = NULL, Post $post = NULL, Common $common, $isTable = true) {
        $this->payment = $payment;
        $this->label = $label;
        $this->bank = $bank;
        $this->cash = $cash;
        $this->paypal = $paypal;
        $this->post = $post;
        $this->payment_data = new ArrayCollection();
        $this->common = $common;
        $this->isTable = $isTable;
        $this->setDatas();
    }

    /**
     *
     * @return type $payment
     */
    private function getPayment() {
        return $this->payment;
    }

    /**
     *
     * @return type $label
     */
    private function getLabel() {
        return $this->label;
    }

    /**
     *
     * @return type $bank
     */
    private function getBank() {
        return $this->bank;
    }

    /**
     *
     * @return type $cash
     */
    private function getCash() {
        return $this->cash;
    }

    /**
     *
     * @return type paypal
     */
    private function getPaypal() {
        return $this->paypal;
    }

    /**
     *
     * @return type $post
     */
    private function getPost() {
        return $this->post;
    }

    /**
     *
     * @return type common
     */
    private function getCommon() {
        return $this->common;
    }

    /**
     *
     * @return type $field
     */
    public function getDatas() {
        return $this->payment_data;
    }

    /**
     * set Data of payment
     */
    private function setDatas() {
        if (!is_null($this->getBank())) {
            $this->setBankData();
        } elseif (!is_null($this->getCash())) {
            $this->setCashData();
        } elseif (!is_null($this->getPaypal())) {
            $this->setPaypalData();
        } elseif (!is_null($this->getPost())) {
            $this->setPostData();
        }
    }

    /**
     *
     * @return type $paymentid
     */
    public function getPaymentId() {
        return $this->getPayment()->getId();
    }

    /**
     *
     * @return type $paymentname
     */
    public function getPaymentName() {
        return $this->getPayment()->getName();
    }

    /**
     *
     * @return type getpaymentshortname
     */
    public function getPaymentShortName() {
        return $this->getPayment()->getShortName();
    }

    /**
     *
     * @return type $title
     */
    public function getTitle() {
        return $this->getLabel()->getTitle();
    }

    /**
     *
     * @return type $short_text
     */
    public function getShortText() {
        return $this->getLabel()->getShortText();
    }

    /**
     *
     * @return type $image
     */
    public function getImage() {
        if (!is_null($this->getLabel()->getImage())) {
            return $this->getLabel()->getImage()->getSize50()->getPath() . $this->getLabel()->getImage()->getName();
        } else {
            return NULL;
        }
    }

    /**
     * @return type $isTable
     */
    public function getIsTable() {
        return $this->isTable;
    }

    /**
     * set all bank data to array
     */
    private function setBankData() {
        $this->payment_data[] = new Field('Account holder', $this->getBank()->getAccountHolder());
        $this->payment_data[] = new Field('Account Number', $this->getBank()->getAccountNumber());
        $this->payment_data[] = new Field('Sort code', $this->getBank()->getSortCode());
        $this->payment_data[] = new Field('Bank name', $this->getBank()->getBankName());
        $this->payment_data[] = new Field('IBAN', $this->getBank()->getIban());
        $this->payment_data[] = new Field('Bic', $this->getBank()->getBic());
    }

    /**
     * set all cash data to array
     */
    private function setCashData() {
        $this->payment_data[] = new Field('Price', $this->getCash()->getPrice(), $this->getCommon()->getCurrencyCode());
    }

    /**
     * set all paypal data to array
     */
    private function setPaypalData() {
        $this->payment_data = array();
    }

    /**
     * set all post data to array
     */
    private function setPostData() {
        $this->payment_data = array();
    }

}
