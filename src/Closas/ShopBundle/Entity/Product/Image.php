<?php

namespace Closas\ShopBundle\Entity\Product;

use Closas\ShopBundle\Entity\Product;
use Closas\ShopBundle\Entity\Product\Image\Size;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_image")
 */
class Image {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Product", inversedBy="images")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

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
     * @ORM\ManyToOne(targetEntity="Closas\ShopBundle\Entity\Product\Image\Size", inversedBy="images")
     * @ORM\JoinColumn(name="size_id", referencedColumnName="id")
     */
    private $size;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Image
     */
    public function setOriginalName($originalName)
    {
        $this->original_name = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    /**
     * Set mimetyp
     *
     * @param string $mimetyp
     *
     * @return Image
     */
    public function setMimetyp($mimetyp)
    {
        $this->mimetyp = $mimetyp;

        return $this;
    }

    /**
     * Get mimetyp
     *
     * @return string
     */
    public function getMimetyp()
    {
        return $this->mimetyp;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return Image
     */
    public function setIsDefault($isDefault)
    {
        $this->is_default = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * Set product
     *
     * @param \Closas\ShopBundle\Entity\Product $product
     *
     * @return Image
     */
    public function setProduct(\Closas\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Closas\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set size
     *
     * @param \Closas\ShopBundle\Entity\Product\Image\Size $size
     *
     * @return Image
     */
    public function setSize(\Closas\ShopBundle\Entity\Product\Image\Size $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return \Closas\ShopBundle\Entity\Product\Image\Size
     */
    public function getSize()
    {
        return $this->size;
    }
}
