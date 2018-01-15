<?php

namespace Closas\ShopBundle\Entity;

use Closas\ShopBundle\Entity\Image\Size;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image")
 */
class Image {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $original_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $mimetyp;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_default;

    /**
     * @ORM\ManyToMany(targetEntity="Closas\ShopBundle\Entity\Image\Size", inversedBy="images")
     * @ORM\JoinTable(name="images_sizes")
     */
    private $sizes;

    /**
     * @ORM\ManyToMany(targetEntity="Closas\ShopBundle\Entity\Content", mappedBy="images")
     */
    private $contents;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Category", inversedBy="images")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Payment\Lang\Label", mappedBy="image")
     */
    private $paymentlabels;

//    /**
//     * Image file
//     *
//     * @var File
//     *
//     * @Assert\File(
//     *     maxSize = "5M",
//     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
//     *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
//     *     mimeTypesMessage = "Only the filetypes image are allowed."
//     * )
//     */
//    protected $file;
    /**
     * Constructor
     */
    public function __construct() {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Image
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
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Image
     */
    public function setOriginalName($originalName) {
        $this->original_name = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName() {
        return $this->original_name;
    }

    /**
     * Set mimetyp
     *
     * @param string $mimetyp
     *
     * @return Image
     */
    public function setMimetyp($mimetyp) {
        $this->mimetyp = $mimetyp;

        return $this;
    }

    /**
     * Get mimetyp
     *
     * @return string
     */
    public function getMimetyp() {
        return $this->mimetyp;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return Image
     */
    public function setIsDefault($isDefault) {
        $this->is_default = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault() {
        return $this->is_default;
    }

    /**
     * Add size
     *
     * @param \Closas\ShopBundle\Entity\Image\Size $size
     *
     * @return Image
     */
    public function addSize(\Closas\ShopBundle\Entity\Image\Size $size) {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param \Closas\ShopBundle\Entity\Image\Size $size
     */
    public function removeSize(\Closas\ShopBundle\Entity\Image\Size $size) {
        $this->sizes->removeElement($size);
    }

    /**
     * Get sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes() {
        return $this->sizes;
    }

    /**
     * Get size 50
     *
     * @return type size
     */
    public function getSize50() {
        return $this->sizes[0];
    }

    /**
     * Get size 80
     *
     * @return type size
     */
    public function getSize80() {
        return $this->sizes[1];
    }

    /**
     * Get size 200
     *
     * @return type size
     */
    public function getSize200() {
        return $this->sizes[2];
    }

    /**
     * Get size 500
     *
     * @return type size
     */
    public function getSize500() {
        return $this->sizes[3];
    }

    /**
     * Get size 800
     *
     * @return type size
     */
    public function getSize800() {
        return $this->sizes[4];
    }

    /**
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return Image
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
     * Set category
     *
     * @param \Closas\ShopBundle\Entity\Category $category
     *
     * @return Image
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
     * Add paymentlabel
     *
     * @param \Closas\ShopBundle\Entity\Payment\Lang\Label $paymentlabel
     *
     * @return Image
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

}
