<?php

namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_post")
 */
class Post {

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
    private $pspid;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $secret_key;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $bg_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $txt_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $table_bg_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $table_txt_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $button_bg_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $button_txt_color;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $font_type;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $email_fields;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $is_iframe = false;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $iframe_name;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $iframe_width;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $iframe_height;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Payment", inversedBy="posts")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment\Lang\Label", mappedBy="post")
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
     * @return Post
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
     * Set pspid
     *
     * @param string $pspid
     *
     * @return Post
     */
    public function setPspid($pspid) {
        $this->pspid = $pspid;

        return $this;
    }

    /**
     * Get pspid
     *
     * @return string
     */
    public function getPspid() {
        return $this->pspid;
    }

    /**
     * Set secretKey
     *
     * @param string $secretKey
     *
     * @return Post
     */
    public function setSecretKey($secretKey) {
        $this->secret_key = $secretKey;

        return $this;
    }

    /**
     * Get secretKey
     *
     * @return string
     */
    public function getSecretKey() {
        return $this->secret_key;
    }

    /**
     * Set bgColor
     *
     * @param string $bgColor
     *
     * @return Post
     */
    public function setBgColor($bgColor) {
        $this->bg_color = $bgColor;

        return $this;
    }

    /**
     * Get bgColor
     *
     * @return string
     */
    public function getBgColor() {
        return $this->bg_color;
    }

    /**
     * Set txtColor
     *
     * @param string $txtColor
     *
     * @return Post
     */
    public function setTxtColor($txtColor) {
        $this->txt_color = $txtColor;

        return $this;
    }

    /**
     * Get txtColor
     *
     * @return string
     */
    public function getTxtColor() {
        return $this->txt_color;
    }

    /**
     * Set tableBgColor
     *
     * @param string $tableBgColor
     *
     * @return Post
     */
    public function setTableBgColor($tableBgColor) {
        $this->table_bg_color = $tableBgColor;

        return $this;
    }

    /**
     * Get tableBgColor
     *
     * @return string
     */
    public function getTableBgColor() {
        return $this->table_bg_color;
    }

    /**
     * Set tableTxtColor
     *
     * @param string $tableTxtColor
     *
     * @return Post
     */
    public function setTableTxtColor($tableTxtColor) {
        $this->table_txt_color = $tableTxtColor;

        return $this;
    }

    /**
     * Get tableTxtColor
     *
     * @return string
     */
    public function getTableTxtColor() {
        return $this->table_txt_color;
    }

    /**
     * Set buttonBgColor
     *
     * @param string $buttonBgColor
     *
     * @return Post
     */
    public function setButtonBgColor($buttonBgColor) {
        $this->button_bg_color = $buttonBgColor;

        return $this;
    }

    /**
     * Get buttonBgColor
     *
     * @return string
     */
    public function getButtonBgColor() {
        return $this->button_bg_color;
    }

    /**
     * Set buttonTxtColor
     *
     * @param string $buttonTxtColor
     *
     * @return Post
     */
    public function setButtonTxtColor($buttonTxtColor) {
        $this->button_txt_color = $buttonTxtColor;

        return $this;
    }

    /**
     * Get buttonTxtColor
     *
     * @return string
     */
    public function getButtonTxtColor() {
        return $this->button_txt_color;
    }

    /**
     * Set fontType
     *
     * @param string $fontType
     *
     * @return Post
     */
    public function setFontType($fontType) {
        $this->font_type = $fontType;

        return $this;
    }

    /**
     * Get fontType
     *
     * @return string
     */
    public function getFontType() {
        return $this->font_type;
    }

    /**
     * Set emailFields
     *
     * @param string $emailFields
     *
     * @return Post
     */
    public function setEmailFields($emailFields) {
        $this->email_fields = $emailFields;

        return $this;
    }

    /**
     * Get emailFields
     *
     * @return string
     */
    public function getEmailFields() {
        return $this->email_fields;
    }

    /**
     * Set isIframe
     *
     * @param boolean $isIframe
     *
     * @return Post
     */
    public function setIsIframe($isIframe) {
        $this->is_iframe = $isIframe;

        return $this;
    }

    /**
     * Get isIframe
     *
     * @return boolean
     */
    public function getIsIframe() {
        return $this->is_iframe;
    }

    /**
     * Set iframeName
     *
     * @param string $iframeName
     *
     * @return Post
     */
    public function setIframeName($iframeName) {
        $this->iframe_name = $iframeName;

        return $this;
    }

    /**
     * Get iframeName
     *
     * @return string
     */
    public function getIframeName() {
        return $this->iframe_name;
    }

    /**
     * Set iframeWidth
     *
     * @param string $iframeWidth
     *
     * @return Post
     */
    public function setIframeWidth($iframeWidth) {
        $this->iframe_width = $iframeWidth;

        return $this;
    }

    /**
     * Get iframeWidth
     *
     * @return string
     */
    public function getIframeWidth() {
        return $this->iframe_width;
    }

    /**
     * Set iframeHeight
     *
     * @param string $iframeHeight
     *
     * @return Post
     */
    public function setIframeHeight($iframeHeight) {
        $this->iframe_height = $iframeHeight;

        return $this;
    }

    /**
     * Get iframeHeight
     *
     * @return string
     */
    public function getIframeHeight() {
        return $this->iframe_height;
    }

    /**
     * Set payment
     *
     * @param \App\Entity\Payment $payment
     *
     * @return Post
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
     * @return Post
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
     * @return Post
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

    /**
     * Set pspid
     *
     * @param string $title
     *
     * @return string
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

}
