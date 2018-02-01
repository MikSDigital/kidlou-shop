<?php

namespace App\RepositoryClasses\Product;

use Doctrine\Common\Collections\ArrayCollection;
use App\RepositoryClasses\Image;

class Children {

    /**
     *
     * @var type array
     */
    private $data = array();

    /**
     *
     * @var type array
     */
    private $image_items = array();

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     *
     * @return type integer
     */
    public function getId() {
        return $this->data['children_id'];
    }

    /**
     *
     * @return type string
     */
    public function getName() {
        return $this->data['children_name'];
    }

    /**
     *
     * @return type string
     */
    public function getPrice() {
        return $this->data['children_price'];
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

}
