<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Product\Description;
use Closas\ShopBundle\Entity\Nivoslider;
use Closas\ShopBundle\Entity\Gift\Text;

/**
 * @ORM\Entity
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Language")
 */
class Language {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $short_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Product\Description", mappedBy="lang")
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Nivoslider\Item", mappedBy="lang")
     */
    private $nivosliders;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Category\Label", mappedBy="lang")
     */
    private $labels;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Content", mappedBy="lang")
     */
    private $contents;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Lang\Label", mappedBy="lang")
     */
    private $paymentlabels;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Order", mappedBy="lang")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="Closas\UserBundle\Entity\User\Personal", mappedBy="lang")
     */
    private $personals;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Gift\Text", mappedBy="lang")
     */
    private $gifttexts;

    /**
     * Constructor
     */
    public function __construct() {
        $this->descriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->labels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->personals = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gifttexts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Language
     */
    public function setShortName($shortName) {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName() {
        return $this->short_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Language
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add description
     *
     * @param \Closas\ShopBundle\Entity\Product\Description $description
     *
     * @return Language
     */
    public function addDescription(\Closas\ShopBundle\Entity\Product\Description $description) {
        $this->descriptions[] = $description;

        return $this;
    }

    /**
     * Remove description
     *
     * @param \Closas\ShopBundle\Entity\Product\Description $description
     */
    public function removeDescription(\Closas\ShopBundle\Entity\Product\Description $description) {
        $this->descriptions->removeElement($description);
    }

    /**
     * Get descriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescriptions() {
        return $this->descriptions;
    }

    /**
     * Add label
     *
     * @param \Closas\ShopBundle\Entity\Category\Label $label
     *
     * @return Language
     */
    public function addLabel(\Closas\ShopBundle\Entity\Category\Label $label) {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Remove label
     *
     * @param \Closas\ShopBundle\Entity\Category\Label $label
     */
    public function removeLabel(\Closas\ShopBundle\Entity\Category\Label $label) {
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
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return Language
     */
    public function addContent(\Closas\ShopBundle\Entity\Content $content) {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     */
    public function removeContent(\Closas\ShopBundle\Entity\Content $content) {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Add nivoslider
     *
     * @param \Closas\ShopBundle\Entity\Nivoslider\Item $nivoslider
     *
     * @return Language
     */
    public function addNivoslider(\Closas\ShopBundle\Entity\Nivoslider\Item $nivoslider) {
        $this->nivosliders[] = $nivoslider;

        return $this;
    }

    /**
     * Remove nivoslider
     *
     * @param \Closas\ShopBundle\Entity\Nivoslider\Item $nivoslider
     */
    public function removeNivoslider(\Closas\ShopBundle\Entity\Nivoslider\Item $nivoslider) {
        $this->nivosliders->removeElement($nivoslider);
    }

    /**
     * Get nivosliders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNivosliders() {
        return $this->nivosliders;
    }

    /**
     * Add paymentlabel
     *
     * @param \Closas\ShopBundle\Entity\Payment\Lang\Label $paymentlabel
     *
     * @return Language
     */
    public function addPaymentlabel(\Closas\ShopBundle\Entity\Payment\Lang\Label $paymentlabel) {
        $this->paymentlabels[] = $paymentlabel;

        return $this;
    }

    /**
     * Remove paymentlabel
     *
     * @param \Closas\ShopBundle\Entity\Payment\Lang\Label $paymentlabel
     */
    public function removePaymentlabel(\Closas\ShopBundle\Entity\Payment\Lang\Label $paymentlabel) {
        $this->paymentlabels->removeElement($paymentlabel);
    }

    /**
     * Get paymentlabels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentlabels() {
        return $this->paymentlabels;
    }

    /**
     * Add order
     *
     * @param \Closas\ShopBundle\Entity\Order $order
     *
     * @return Language
     */
    public function addOrder(\Closas\ShopBundle\Entity\Order $order) {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \Closas\ShopBundle\Entity\Order $order
     */
    public function removeOrder(\Closas\ShopBundle\Entity\Order $order) {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders() {
        return $this->orders;
    }

    /**
     * Add personal
     *
     * @param \Closas\UserBundle\Entity\User\Personal $personal
     *
     * @return Language
     */
    public function addPersonal(\Closas\UserBundle\Entity\User\Personal $personal) {
        $this->personals[] = $personal;

        return $this;
    }

    /**
     * Remove personal
     *
     * @param \Closas\UserBundle\Entity\User\Personal $personal
     */
    public function removePersonal(\Closas\UserBundle\Entity\User\Personal $personal) {
        $this->personals->removeElement($personal);
    }

    /**
     * Get personals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonals() {
        return $this->personals;
    }

    /**
     * Add gifttext
     *
     * @param \Closas\ShopBundle\Entity\Gift\Text $text
     *
     * @return Language
     */
    public function addGifttexts(\Closas\ShopBundle\Entity\Gift\Text $text) {
        $this->gifttexts[] = $text;

        return $this;
    }

    /**
     * Remove gifttext
     *
     * @param \Closas\ShopBundle\Entity\Gift\Text $text
     */
    public function removeGifttexts(\Closas\ShopBundle\Entity\Gift\Text $text) {
        $this->gifttexts->removeElement($text);
    }

    /**
     * Get gifttexts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGifttexts() {
        return $this->gifttexts;
    }

    public function __toString() {
        return $this->id;
    }

}
