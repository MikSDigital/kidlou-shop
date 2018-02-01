<?php

namespace App\RepositoryClasses;

use Doctrine\Common\Collections\ArrayCollection;
use App\RepositoryClasses\Product\Children;
use App\RepositoryClasses\Image;

class Product {

    /**
     *
     * @var type array
     */
    private $data = array();

    /**
     *
     * @var type array
     */
    private $children_items = array();

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

    /**
     *
     * @return type float
     */
    public function getPrice() {
        return $this->data['value'];
    }

    /**
     *
     * @return type string
     */
    public function getIndicies() {
        return $this->data['indicies'];
    }

    /**
     *
     * @return type string
     */
    public function getAccessoires() {
        return $this->data['accessoires'];
    }

    /**
     *
     * @return type array
     */
    public function getImages() {
        return explode(',', $this->data['product_images']);
    }

    public function addImages($data) {
        $arr_data = explode(',', $data);
        foreach ($arr_data as $data) {

        }
    }

    /**
     *
     * @param type $data
     * @return $this
     */
    public function addChildren($data) {
        $this->children_items[] = new Children($data);
        return $this;
    }

    /**
     *
     * @return type array
     */
    public function getChildren() {
        return $this->children_items;
    }

}
