<?php

namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_paypal")
 */
class Paypal {

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
    private $email_account = null;

    /**
     * @ORM\Column(type="integer", length=5, options={"default":0})
     */
    private $authentication_methods = 0;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $client_id = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $secret_key = null;

    /**
     * @ORM\Column(type="integer", length=5, options={"default":0})
     */
    private $sandbox_mode = 0;

    /**
     * @ORM\Column(type="integer", length=5, options={"default":0})
     */
    private $api_use_proxy = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Payment", inversedBy="paypals")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment\Lang\Label", mappedBy="paypal")
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
     * @return Paypal
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
     * Set emailAccount
     *
     * @param string $emailAccount
     *
     * @return Paypal
     */
    public function setEmailAccount($emailAccount) {
        $this->email_account = $emailAccount;

        return $this;
    }

    /**
     * Get emailAccount
     *
     * @return string
     */
    public function getEmailAccount() {
        return $this->email_account;
    }

    /**
     * Set authenticationMethods
     *
     * @param integer $authenticationMethods
     *
     * @return Paypal
     */
    public function setAuthenticationMethods($authenticationMethods) {
        $this->authentication_methods = $authenticationMethods;

        return $this;
    }

    /**
     * Get authenticationMethods
     *
     * @return integer
     */
    public function getAuthenticationMethods() {
        return $this->authentication_methods;
    }

    /**
     * Set client id
     *
     * @param string $client_id
     *
     * @return Paypal
     */
    public function setClientId($client_id) {
        $this->client_id = $client_id;

        return $this;
    }

    /**
     * Get client id
     *
     * @return string
     */
    public function getClientId() {
        return $this->client_id;
    }

    /**
     * Set secret key
     *
     * @param string $secret_key
     *
     * @return Paypal
     */
    public function setSecretKey($secret_key) {
        $this->secret_key = $secret_key;

        return $this;
    }

    /**
     * Get secret key
     *
     * @return string
     */
    public function getSecretKey() {
        return $this->secret_key;
    }

    /**
     * Set sandboxMode
     *
     * @param integer $sandboxMode
     *
     * @return Paypal
     */
    public function setSandboxMode($sandboxMode) {
        $this->sandbox_mode = $sandboxMode;

        return $this;
    }

    /**
     * Get sandboxMode
     *
     * @return integer
     */
    public function getSandboxMode() {
        return $this->sandbox_mode;
    }

    /**
     * Set apiUseProxy
     *
     * @param integer $apiUseProxy
     *
     * @return Paypal
     */
    public function setApiUseProxy($apiUseProxy) {
        $this->api_use_proxy = $apiUseProxy;

        return $this;
    }

    /**
     * Get apiUseProxy
     *
     * @return integer
     */
    public function getApiUseProxy() {
        return $this->api_use_proxy;
    }

    /**
     * Set payment
     *
     * @param \App\Entity\Payment $payment
     *
     * @return Paypal
     */
    public function setPayment(\App\Entity\Payment $payment = null) {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \App\Entity\Payment
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Add label
     *
     * @param \App\Entity\Payment\Lang\Label $label
     *
     * @return Paypal
     */
    public function addLabel(\App\Entity\Payment\Lang\Label $label) {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Remove label
     *
     * @param \App\Entity\Payment\Lang\Label $label
     */
    public function removeLabel(\App\Entity\Payment\Lang\Label $label) {
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
     * @return Paypal
     */
    public function setFormularUrl($formularUrl) {
        $this->formular_url = $formularUrl;

        return $this;
    }

    /**
     * Get formularUrl
     *
     * @return string
     */
    public function getFormularUrl() {
        return $this->formular_url;
    }

}
