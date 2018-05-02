<?php

namespace App\Service\Calendar;

class Day {

    /**
     *
     * @var type day
     */
    private $day;

    /**
     *
     * @var type month
     */
    private $month;

    /**
     *
     * @var type year
     */
    private $year;

    /**
     *
     * @var type request
     */
    private $request;

    /**
     *
     * @var type deliver_standard
     */
    private $deliver_standard;

    /**
     *
     * @var type deliver_standard
     */
    private $deliver_exception;

    /**
     *
     * @var type quote calendar
     */
    private $current_quote_calendar;

    /**
     *
     * @var type
     */
    private $css_deliver = 'deliver';

    /**
     *
     * @var type
     */
    private $css_no_deliver = 'nodeliver';

    /**
     *
     * @var type
     */
    private $css_reserved = 'reserved';

    /**
     *
     * @var type
     */
    private $css_empty_deliver = 'emptydeliver';

    public function __construct($day, $month, $year, $request, $deliver_standard, $deliver_exception, $current_quote_calendar) {
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
        $this->request = $request;
        $this->deliver_standard = $deliver_standard;
        $this->deliver_exception = $deliver_exception;
        $this->current_quote_calendar = $current_quote_calendar;
    }

    /**
     *
     * @return type integer
     */
    public function getDay() {
        return ($this->day < 10) ? '0' . $this->day : $this->day;
    }

    /**
     *
     * @return type array
     */
    public function getArrDay() {
        $day = ($this->day < 10) ? '0' . $this->day : $this->day;
        $arr_day = array();
        $arr_day[0] = substr($day, 0, 1);
        $arr_day[1] = substr($day, 1, 1);
        return $arr_day;
    }

    /**
     *
     * @return type integer
     */
    public function getDayId() {
        return $this->day;
    }

    /**
     *
     * @return type string
     */
    public function getMonth() {
        return $this->month;
    }

    /**
     *
     * @return type integer
     */
    public function getYear() {
        return $this->year;
    }

    /**
     *
     * @return type request
     */
    private function getRequest() {
        return $this->request;
    }

    /**
     *
     * @return type array
     */
    private function getCurrentQuoteCalendar() {
        return $this->current_quote_calendar;
    }

    /**
     *
     * @return type string
     */
    public function getName() {
        $date = new \DateTime($this->getDate());
        $formatter = new \IntlDateFormatter($this->getRequest()->getLocale(), \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
        //$formatter->setPattern("eeeeee");
        $formatter->setPattern("ccc");
        return $formatter->format($date);
    }

    /**
     *
     * @return type integer
     */
    public function getWeekdayId() {
        $date = new \DateTime($this->getDate());
        return $date->format('N');
    }

    /**
     *
     * @return type date
     */
    public function getDate() {
        return $this->getYear() . '-' . $this->getMonth() . '-' . $this->getDay();
    }

    /**
     *
     * @return type string
     */
    public function getCss() {
        // check if date is past
        $date = new \DateTime($this->getDate());
        $today = new \DateTime('now');
        if ($date <= $today) {
            return $this->getEmptyDeliver();
        }

        foreach ($this->getCurrentQuoteCalendar() as $currentQuoteDate) {
            if ($this->getDate() == $currentQuoteDate) {
                return $this->getCssReserved();
            }
        }
        $isException = $this->getIsDateOfException();
        if ($this->getWeekdayId() == 1) {
            if ($this->getDeliverStandard()->getMonday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 2) {
            if ($this->getDeliverStandard()->getTuesday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 3) {
            if ($this->getDeliverStandard()->getWednesday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 4) {
            if ($this->getDeliverStandard()->getThursday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 5) {
            if ($this->getDeliverStandard()->getFriday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 6) {
            if ($this->getDeliverStandard()->getSaturday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
        if ($this->getWeekdayId() == 7) {
            if ($this->getDeliverStandard()->getSunday()) {
                if ($isException) {
                    if (!$isException->getIsDeliver()) {
                        return $this->getCssNoDeliver();
                    }
                }
                return $this->getCssDeliver();
            } else {
                if ($isException) {
                    if ($isException->getIsDeliver()) {
                        return $this->getCssDeliver();
                    }
                }
                return $this->getCssNoDeliver();
            }
        }
    }

    /**
     *
     * @return type deliver_standard
     */
    private function getDeliverStandard() {
        return $this->deliver_standard;
    }

    /**
     *
     * @return type deliver_standard
     */
    private function getDeliverException() {
        return $this->deliver_exception;
    }

    /**
     *
     * @return type deliver
     */
    private function getIsDateOfException() {
        foreach ($this->getDeliverException() as $deliver) {
            if ($deliver->getDate()->format('Y-m-d') == $this->getDate()) {
                return $deliver;
            }
        }
        return false;
    }

    /**
     *
     * @return type string
     */
    private function getCssDeliver() {
        return $this->css_deliver;
    }

    /**
     *
     * @return type string
     */
    private function getCssNoDeliver() {
        return $this->css_no_deliver;
    }

    /**
     *
     * @return type string
     */
    private function getCssReserved() {
        return $this->css_reserved;
    }

    /**
     *
     * @return type string
     */
    private function getEmptyDeliver() {
        return $this->css_empty_deliver;
    }

}
