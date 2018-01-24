<?php

namespace App\Service;

class Parameter {

    /**
     *
     * @var type string
     */
    private $locale;

    /**
     *
     * @var type array
     */
    private $languages;

    /**
     *
     * @var type string
     */
    private $adminPathName;

    public function __construct($locale, $languages, $adminPathName) {
        $this->locale = $locale;
        $this->languages = $languages;
        $this->adminPathName = $adminPathName;
    }

    /**
     *
     * @return type
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     *
     * @return type
     */
    public function getLanguages() {
        return $this->languages;
    }

    /**
     *
     * @return type
     */
    public function getAdminPathName() {
        return $this->adminPathName;
    }

}
