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

    /**
     *
     * @var type array
     */
    private $image_items = array();

    /**
     *
     * @param type $data
     */
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
    public function getUrlKey() {
        return $this->data['url_key'];
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
     * @param type $original_name
     * @param type $name
     * @return $this
     */
    public function addImages($original_name, $name) {
        $this->image_items[] = new Image($original_name, $name);
        return $this;
    }

    /**
     *
     * @return type array
     */
    public function getImages() {
        return $this->image_items;
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

    /**
     *
     * @return boolean
     */
    public function getCurrentChildren() {
        $key = count($this->children_items) - 1;
        if ($key < 0) {
            return FALSE;
        }
        return $this->children_items[$key];
    }

}
