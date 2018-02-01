<?php

namespace App\RepositoryClasses;

use Doctrine\Common\Collections\ArrayCollection;

class Image {

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
    public function getName() {
        return $this->data['id'];
    }

    public function getIsDefault() {
        return;
    }

}
