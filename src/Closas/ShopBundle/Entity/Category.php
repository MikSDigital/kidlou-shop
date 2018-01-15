<?php

namespace Closas\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Entity\Category\Label;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Closas\ShopBundle\Entity\Repository\Category")
 */
class Category {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url_key;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=5)
     */
    private $level;

    /**
     * @ORM\Column(name="`order`", type="integer", length=5)
     */
    private $order;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent_id;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Category\Label", mappedBy="category")
     */
    private $labels;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Image", mappedBy="category")
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="Closas\ShopBundle\Entity\Product", inversedBy="categories")
     * @ORM\JoinTable(name="categories_products")
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity="Closas\ShopBundle\Entity\Content", inversedBy="categories")
     * @ORM\JoinTable(name="categories_contents")
     */
    private $contents;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Category\Typ", inversedBy="categories")
     * @ORM\JoinColumn(name="typ_id", referencedColumnName="id")
     */
    private $typ;

    public function __construct() {
        $this->labels = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->contents = new ArrayCollection();
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
     * Set urlKey
     *
     * @param string $urlKey
     *
     * @return Category
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

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Category
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
     * Set level
     *
     * @param integer $level
     *
     * @return Category
     */
    public function setLevel($level) {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Category
     */
    public function setOrder($order) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Category
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
     * Add label
     *
     * @param \Closas\ShopBundle\Entity\Category\Label $label
     *
     * @return Category
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
     * Add product
     *
     * @param \Closas\ShopBundle\Entity\Product $product
     *
     * @return Category
     */
    public function addProduct(\Closas\ShopBundle\Entity\Product $product) {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Closas\ShopBundle\Entity\Product $product
     */
    public function removeProduct(\Closas\ShopBundle\Entity\Product $product) {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return Category
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
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    public function getContents() {

        return $this->contents;
    }

    /**
     * Set typ
     *
     * @param \Closas\ShopBundle\Entity\Category\Typ $typ
     *
     * @return Category
     */
    public function setTyp(\Closas\ShopBundle\Entity\Category\Typ $typ = null) {
        $this->typ = $typ;

        return $this;
    }

    /**
     * Get typ
     *
     * @return \Closas\ShopBundle\Entity\Category\Typ
     */
    public function getTyp() {
        return $this->typ;
    }

    /**
     * Add image
     *
     * @param \Closas\ShopBundle\Entity\Image $image
     *
     * @return Category
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

}
