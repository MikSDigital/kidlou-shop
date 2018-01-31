<?php

use Doctrine\Common\Collections\ArrayCollection;

namespace App\RepositoryClasses;

class Product {

    /**
     *
     * @var type integer
     */
    private $data = array();

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     *
     * @return type integer
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     *
     * @return type integer
     */
    public function getProductId() {
        return $this->data['product_id'];
    }

    /**
     *
     * @return type string
     */
    public function getSku() {
        return $this->data['sku'];
    }

    /**
     *
     * @return type string
     */
    public function getName() {
        return $this->data['name'];
    }

    /**
     *
     * @return type string
     */
    public function getLongText() {
        return $this->data['long_text'];
    }

    /**
     *
     * @return type string
     */
    public function getShortText() {
        return $this->data['short_text'];
    }

}
