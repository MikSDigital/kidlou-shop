<?php

namespace App\RepositoryClasses;

use Doctrine\Common\Collections\ArrayCollection;

class Image {

    /**
     *
     * @var type string
     */
    private $original_name;

    /**
     *
     * @var type string
     */
    private $name;

    public function __construct($original_name, $name) {
        $this->original_name = $original_name;
        $this->name = $name;
    }

    /**
     *
     * @return type string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @return type string
     */
    public function getOriginalName() {
        return $this->original_name;
    }

    /**
     *
     * @return boolean
     */
    public function getIsDefault() {
        $pos = strpos($this->original_name, '-default');
        if ($pos !== FALSE) {
            return TRUE;
        }
        return FALSE;
    }

}
