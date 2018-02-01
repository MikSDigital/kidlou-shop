<?php

namespace App\RepositoryClasses\Product;

use Doctrine\Common\Collections\ArrayCollection;

class Children {

    /**
     *
     * @var type array
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
        return $this->data['children_id'];
    }

    /**
     *
     * @return type integer
     */
    public function getImage() {
        return $this->data['children_image'];
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

}
