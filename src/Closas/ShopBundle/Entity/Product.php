<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
//use Closas\ShopBundle\Entity\Product\Image;
use Closas\ShopBundle\Entity\Price;
use Closas\ShopBundle\Entity\Language;
use Closas\ShopBundle\Entity\Product\Description;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Product")
 */
class Product {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $sku;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=4, options={"default":0})
     */
    private $sale;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $url_key;

    /**
     * @ORM\OneToOne(targetEntity="Closas\ShopBundle\Entity\Price", mappedBy="product")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Product\Image", mappedBy="product")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Product\Description", mappedBy="product")
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Calendar", mappedBy="product")
     */
    private $calendars;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Product\Typ", inversedBy="products")
     * @ORM\JoinColumn(name="typ_id", referencedColumnName="id")
     */
    private $typ;

    /**
     * @ORM\ManyToMany(targetEntity="Closas\ShopBundle\Entity\Category", mappedBy="products")
     */
    private $categories;

    /**
     * encrypt string
     * @var string
     */
    private $encrypt;

    /**
     * description
     * @var description
     */
    private $description;

    public function __construct() {
        $this->images = new ArrayCollection();
        $this->descriptions = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku) {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * Set price
     *
     * @param \Closas\ShopBundle\Entity\Price $price
     *
     * @return Product
     */
    public function setPrice(\Closas\ShopBundle\Entity\Price $price = null) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return \Closas\ShopBundle\Entity\Price
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Add image
     *
     * @param \Closas\ShopBundle\Entity\Product\Image $image
     *
     * @return Product
     */
    public function addImage(\Closas\ShopBundle\Entity\Product\Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Closas\ShopBundle\Entity\Product\Image $image
     */
    public function removeImage(\Closas\ShopBundle\Entity\Product\Image $image) {
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
     * Add description
     *
     * @param \Closas\ShopBundle\Entity\Product\Description $description
     *
     * @return Product
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
     * Set typ
     *
     * @param \Closas\ShopBundle\Entity\Product\Typ $typ
     *
     * @return Product
     */
    public function setTyp(\Closas\ShopBundle\Entity\Product\Typ $typ = null) {
        $this->typ = $typ;

        return $this;
    }

    /**
     * Get typ
     *
     * @return \Closas\ShopBundle\Entity\Product\Typ
     */
    public function getTyp() {
        return $this->typ;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Product
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
     * Set category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\Closas\ShopBundle\Entity\Category $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Closas\ShopBundle\Entity\Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Product
     */
    public function setParentId($parentId) {
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId() {
        return $this->parent_id;
    }

    /**
     * set encrypt
     * @return product
     */
    public function setEncryptVirtuelId($encrypt) {
        $this->encrypt = $encrypt;
        return $this;
    }

    /**
     * Get virtuel product id
     * @return string
     */
    public function getEncryptVirtuelId() {
        return $this->encrypt;
    }

    /**
     * Set Description by id
     * @return product
     */
    public function setDescription($description_id) {
        $this->description = $this->descriptions[$description_id];
    }

    /**
     * Get description
     * @return description
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set sale
     *
     * @param string $sale
     *
     * @return Product
     */
    public function setSale($sale) {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return string
     */
    public function getSale() {
        return $this->sale;
    }

    /**
     * Add calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     *
     * @return Product
     */
    public function addCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars[] = $calendar;

        return $this;
    }

    /**
     * Remove calendar
     *
     * @param \Closas\ShopBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\Closas\ShopBundle\Entity\Calendar $calendar) {
        $this->calendars->removeElement($calendar);
    }

    /**
     * Get calendars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendars() {
        return $this->calendars;
    }

    /**
     * Add category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     *
     * @return Product
     */
    public function addCategory(\Closas\ShopBundle\Entity\Category $category) {
        $category->addProduct($this);
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     */
    public function removeCategory(\Closas\ShopBundle\Entity\Category $category) {
        $category->removeProduct($this);
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Set urlKey
     *
     * @param string $urlKey
     *
     * @return Product
     */
    public function setUrlKey($urlKey) {
        $this->url_key = $urlKey;

        return $this;
    }

    /**
     * Get urlKey
     *
     * @return string
     */
    public function getUrlKey() {
        return $this->url_key;
    }

}
