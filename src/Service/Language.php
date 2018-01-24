<?php

namespace App\Service;

class Language {

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

    public function __construct($locale, $languages) {
        $this->locale = $locale;
        $this->languages = $languages;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function getLanguages() {
        return $this->languages;
    }

}
