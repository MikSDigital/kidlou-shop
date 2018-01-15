<?php

namespace Closas\ShopBundle\Entity\Payment;

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
    private $api_username = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $api_password = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $api_signature = null;

    /**
     * @ORM\Column(type="integer", length=5, options={"default":0})
     */
    private $sandbox_mode = 0;

    /**
     * @ORM\Column(type="integer", length=5, options={"default":0})
     */
    private $api_use_proxy = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment", inversedBy="paypals")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Lang\Label", mappedBy="paypal")
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
     * Set apiUsername
     *
     * @param string $apiUsername
     *
     * @return Paypal
     */
    public function setApiUsername($apiUsername) {
        $this->api_username = $apiUsername;

        return $this;
    }

    /**
     * Get apiUsername
     *
     * @return string
     */
    public function getApiUsername() {
        return $this->api_username;
    }

    /**
     * Set apiPassword
     *
     * @param string $apiPassword
     *
     * @return Paypal
     */
    public function setApiPassword($apiPassword) {
        $this->api_password = $apiPassword;

        return $this;
    }

    /**
     * Get apiPassword
     *
     * @return string
     */
    public function getApiPassword() {
        return $this->api_password;
    }

    /**
     * Set apiSignature
     *
     * @param string $apiSignature
     *
     * @return Paypal
     */
    public function setApiSignature($apiSignature) {
        $this->api_signature = $apiSignature;

        return $this;
    }

    /**
     * Get apiSignature
     *
     * @return string
     */
    public function getApiSignature() {
        return $this->api_signature;
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
     * @param \Closas\ShopBundle\Entity\Payment $payment
     *
     * @return Paypal
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
     * @return Paypal
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
     * @return Paypal
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
