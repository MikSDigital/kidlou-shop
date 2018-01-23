<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="admin_upload")
 */
class Upload {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload the products as a CSV file.")
     * @Assert\File(mimeTypes={ "text/plain" })
     */
    private $products;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $is_active;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     *
     * @return type id
     */
    public function getId() {
        return $this->id;
    }

    /**
     *
     * @return type $product
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     *
     * @param type $product
     * @return $this
     */
    public function setProducts($products) {
        $this->products = $products;
        return $this;
    }

    /**
     * Get is_active
     *
     * @return type $is_active
     */
    public function getIsActive() {
        return $this->is_active;
    }

    /**
     *
     * @param type $is_active
     * @return Upload
     */
    public function setIsActive($is_active) {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updatetAt
     *
     * @param \DateTime $createdAt
     *
     * @return Upload
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

}
