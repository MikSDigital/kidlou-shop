<?php

namespace Closas\ShopBundle\Helper\Payment;

class Field {

    /**
     *
     * @var type
     */
    private $fieldvalue;

    /**
     *
     * @var type
     */
    private $fieldname;

    /**
     *
     * @var type $currency
     */
    private $currency;

    /**
     *
     * @param \Closas\ShopBundle\Helper\Payment\String $fieldvalue
     * @param \Closas\ShopBundle\Helper\Payment\String $fieldname
     */
    public function __construct($fieldname = '', $fieldvalue = '', $currency = '') {
        $this->fieldname = $fieldname;
        $this->fieldvalue = $fieldvalue;
        $this->currency = $currency;
    }

    /**
     *
     * @return type fieldname
     */
    public function getFieldname() {
        return $this->fieldname;
    }

    /**
     *
     * @return type fieldvalue
     */
    public function getFieldvalue() {
        if ($this->currency != '') {
            return $this->currency . ' ' . $this->fieldvalue;
        }
        return $this->fieldvalue;
    }

}
