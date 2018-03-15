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

    /**
     *
     * @var type string
     */
    private $testPathName;

    public function __construct($locale, $languages, $adminPathName, $apiPathName, $testPathName) {
        $this->locale = $locale;
        $this->languages = $languages;
        $this->adminPathName = $adminPathName;
        $this->apiPathName = $apiPathName;
        $this->testPathName = $testPathName;
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

    /**
     *
     * @return string
     */
    public function getTestPathName() {
        return $this->testPathName;
    }

}
