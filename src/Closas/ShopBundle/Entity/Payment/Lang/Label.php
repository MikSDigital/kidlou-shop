<?php

namespace Closas\ShopBundle\Entity\Payment\Lang;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_label")
 */
class Label {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $title = null;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $website_link = null;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $short_text = null;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment\Bank", inversedBy="labels")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id")
     */
    private $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment\Cash", inversedBy="labels")
     * @ORM\JoinColumn(name="cash_id", referencedColumnName="id")
     */
    private $cash;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment\Paypal", inversedBy="labels")
     * @ORM\JoinColumn(name="paypal_id", referencedColumnName="id")
     */
    private $paypal;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Payment\Post", inversedBy="labels")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Language", inversedBy="paymentlabels")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Image", inversedBy="paymentlabels")
     * @ORM\JoinTable(name="image_id")
     */
    private $image;

    /**
     * Constructor
     */
    public function __construct() {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Label
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

    /**
     * Set websiteLink
     *
     * @param string $websiteLink
     *
     * @return Label
     */
    public function setWebsiteLink($websiteLink) {
        $this->website_link = $websiteLink;

        return $this;
    }

    /**
     * Get websiteLink
     *
     * @return string
     */
    public function getWebsiteLink() {
        return $this->website_link;
    }

    /**
     * Set short_text
     *
     * @param string $short_text
     *
     * @return Label
     */
    public function setShortText($short_text) {
        $this->short_text = $short_text;

        return $this;
    }

    /**
     * Get short_text
     *
     * @return string
     */
    public function getShortText() {
        return $this->short_text;
    }

    /**
     * Add image
     *
     * @param \Closas\ShopBundle\Entity\Image $image
     *
     * @return Label
     */
    public function addImage(\Closas\ShopBundle\Entity\Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Closas\ShopBundle\Entity\Image $image
     */
    public function removeImage(\Closas\ShopBundle\Entity\Image $image) {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Set bank
     *
     * @param \Closas\ShopBundle\Entity\Payment\Bank $bank
     *
     * @return Label
     */
    public function setBank(\Closas\ShopBundle\Entity\Payment\Bank $bank = null) {
        $this->bank = $bank;

        return $this;
    }

    /**
     * Get bank
     *
     * @return \Closas\ShopBundle\Entity\Payment\Bank
     */
    public function getBank() {
        return $this->bank;
    }

    /**
     * Set cash
     *
     * @param \Closas\ShopBundle\Entity\Payment\Cash $cash
     *
     * @return Label
     */
    public function setCash(\Closas\ShopBundle\Entity\Payment\Cash $cash = null) {
        $this->cash = $cash;

        return $this;
    }

    /**
     * Get cash
     *
     * @return \Closas\ShopBundle\Entity\Payment\Cash
     */
    public function getCash() {
        return $this->cash;
    }

    /**
     * Set paypal
     *
     * @param \Closas\ShopBundle\Entity\Payment\Paypal $paypal
     *
     * @return Label
     */
    public function setPaypal(\Closas\ShopBundle\Entity\Payment\Paypal $paypal = null) {
        $this->paypal = $paypal;

        return $this;
    }

    /**
     * Get paypal
     *
     * @return \Closas\ShopBundle\Entity\Payment\Paypal
     */
    public function getPaypal() {
        return $this->paypal;
    }

    /**
     * Set post
     *
     * @param \Closas\ShopBundle\Entity\Payment\Post $post
     *
     * @return Label
     */
    public function setPost(\Closas\ShopBundle\Entity\Payment\Post $post = null) {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Closas\ShopBundle\Entity\Payment\Post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * Set lang
     *
     * @param \Closas\ShopBundle\Entity\Language $lang
     *
     * @return Label
     */
    public function setLang(\Closas\ShopBundle\Entity\Language $lang = null) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \Closas\ShopBundle\Entity\Language
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Get lang
     *
     * @return \Closas\ShopBundle\Entity\Language
     */
    public function getLangShortName() {
        return $this->lang->getShortName();
    }

    /**
     * Set image
     *
     * @param \Closas\ShopBundle\Entity\Image $image
     *
     * @return Label
     */
    public function setImage(\Closas\ShopBundle\Entity\Image $image = null) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Closas\ShopBundle\Entity\Image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Get image
     *
     * @return \Closas\ShopBundle\Entity\Image\Name
     */
    public function getImageName() {
        if ($this->getImage()) {
            return $this->image->getName();
        }
        return NULL;
    }

    /**
     * Get image
     *
     * @return \Closas\ShopBundle\Entity\Image
     */
    public function setImageName(\Closas\ShopBundle\Entity\Image $image = null) {
        return $this->image = $image;
    }

}
