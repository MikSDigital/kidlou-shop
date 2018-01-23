<?php

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;
use App\Entity\Product;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_description")
 * @ORM\Entity(repositoryClass="App\Repository\Product\Description")
 */
class Description {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $long_text;

    /**
     * @ORM\Column(type="text")
     */
    private $short_text;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $indicies;

    /**
     * @ORM\Column(type="string")
     */
    private $accessoires;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="descriptions")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="descriptions")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Description
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
     * Set longText
     *
     * @param string $longText
     *
     * @return Description
     */
    public function setLongText($longText) {
        $this->long_text = $longText;

        return $this;
    }

    /**
     * Get longText
     *
     * @return string
     */
    public function getLongText() {
        return $this->long_text;
    }

    /**
     * Set shortText
     *
     * @param string $shortText
     *
     * @return Description
     */
    public function setShortText($shortText) {
        $this->short_text = $shortText;

        return $this;
    }

    /**
     * Get shortText
     *
     * @return string
     */
    public function getShortText() {
        return $this->short_text;
    }

    /**
     * Set indicies
     *
     * @param string $indicies
     *
     * @return Description
     */
    public function setIndicies($indicies) {
        $this->indicies = $indicies;

        return $this;
    }

    /**
     * Get indicies
     *
     * @return string
     */
    public function getIndicies() {
        return $this->indicies;
    }

    /**
     * Set accessoires
     *
     * @param string $accessoires
     *
     * @return Description
     */
    public function setAccessoires($accessoires) {
        $this->accessoires = $accessoires;

        return $this;
    }

    /**
     * Get accessoires
     *
     * @return string
     */
    public function getAccessoires() {
        return $this->accessoires;
    }

    /**
     * Set lang
     *
     * @param \App\Entity\Language $lang
     *
     * @return Description
     */
    public function setLang(\App\Entity\Language $lang = null) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \App\Entity\Language
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Set product
     *
     * @param \App\Entity\Product $product
     *
     * @return Description
     */
    public function setProduct(\App\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \App\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

}
