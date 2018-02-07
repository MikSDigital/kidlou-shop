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

    /**
     *
     * @var type string
     */
    private $apiPathName;

    public function __construct($locale, $languages, $adminPathName, $apiPathName) {
        $this->locale = $locale;
        $this->languages = $languages;
        $this->adminPathName = $adminPathName;
        $this->apiPathName = $apiPathName;
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
     * @return string
     */
    public function getAdminPathName() {
        return $this->adminPathName;
    }

    /**
     *
     * @return string
     */
    public function getApiPathName() {
        return $this->apiPathName;
    }

}
