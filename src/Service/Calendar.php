<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\Calendar\Day;
use App\Service\Common;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\Entity\Product;
use App\Entity\Quote;
use App\Entity\Order;
use App\Entity\Order\Status;
use App\Entity\Deliver;
use App\Entity\Deliver\Standard;
use App\Entity\Map\QuoteProductAdditional;

class Calendar {

    /**
     * Conection DB
     *
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @var request
     */
    private $request;

    /**
     *
     * @var type container
     */
    private $container;

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var type Dates
     */
    private $dates;

    /**
     *
     * @var type integer
     */
    private $count_day_week = 7;

    /**
     *
     * @var type integer
     */
    private $begin_weekday = 1;

    /**
     *
     * @var type array
     */
    private $days = array();

    /**
     *
     * @var type array
     */
    private $days_crossing = array();

    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Container $container) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->dates = new ArrayCollection();
        $this->container = $container;
        $this->setRequest();
    }

    /**
     *
     * @return type integer
     */
    private function getCountBeforeDays() {
        $date = new \DateTime($this->getYearBefore() . '-' . $this->getMonthBefore() . '-' . '01');
        return $date->format('t');
    }

    /**
     *
     * @return type integer
     */
    private function getCountDays() {
        $date = new \DateTime($this->getYearCurrent() . '-' . $this->getMonthCurrent() . '-' . '01');
        return $date->format('t');
    }

    /**
     *
     * @return type integer
     */
    private function getCountAfterDays() {
        $date = new \DateTime($this->getYearAfter() . '-' . $this->getMonthAfter() . '-' . '01');
        return $date->format('t');
    }

    /**
     * set days
     */
    public function getDays() {
        $deliverStandard = $this->getDeliverStandard();
        $deliverException = $this->getDeliverException();
        $days = $this->getCountDays();
        $all_days = array();
        for ($day = 1; $day <= $days; $day++) {
            $all_days[] = new Day($day, $this->getMonthCurrent(), $this->getYearCurrent(), $this->getRequest(), $deliverStandard, $deliverException, $this->getCurrentCalendar());
        }
        return $all_days;
    }

    /**
     *
     * @return type integer
     */
    private function getFirstWeekdayId() {
        $date = new \DateTime($this->getYearCurrent() . '-' . $this->getMonthCurrent() . '-01');
        return $date->format('N');
    }

    /**
     *
     * @return type integer
     */
    private function getLastWeekdayId() {
        $date = new \DateTime($this->getYearCurrent() . '-' . $this->getMonthCurrent() . '-' . $this->getCountDays());
        return $date->format('N');
    }

    /**
     * set request
     */
    private function setRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     *
     * @return type $request
     */
    private function getRequest() {
        return $this->request;
    }

    /**
     *
     * @return type string
     */
    public function getYearBefore() {
        $year = $this->getYearCurrent();
        $month = (int) $this->getMonthCurrent();
        if ($month == 1) {
            $year--;
        }
        return $year;
    }

    /**
     *
     * @return type string
     */
    public function getYearCurrent() {
        $year = '';
        if (!is_null($this->getRequest())) {
            $year = $this->getRequest()->get('year');
        }
        if ($year == '') {
            $date = new \DateTime('now');
            $year = $date->format('Y');
        }
        return $year;
    }

    /**
     *
     * @return type string
     */
    public function getYearAfter() {
        $year = $this->getYearCurrent();
        $month = (int) $this->getMonthCurrent();
        if ($month == 12) {
            $year++;
        }
        return $year;
    }

    /**
     *
     * @return type string
     */
    public function getMonthBefore() {
        $month = (int) $this->getMonthCurrent();
        if ($month == 1) {
            $month = 12;
        } else {
            $month--;
        }
        $month = (int) $month;
        return ($month < 10) ? '0' . $month : $month;
    }

    /**
     *
     * @return type string
     */
    public function getMonthCurrent() {
        $month = '';
        if (!is_null($this->getRequest())) {
            $month = (int) $this->getRequest()->get('month');
        }
        if ($month == '') {
            $date = new \DateTime('now');
            $month = $date->format('m');
        }
        $month = (int) $month;
        return ($month < 10) ? '0' . $month : $month;
    }

    /**
     *
     * @return type string
     */
    public function getMonthAfter() {
        $month = (int) $this->getMonthCurrent();
        if ($month == 12) {
            $month = 1;
        } else {
            $month++;
        }
        $month = (int) $month;
        return ($month < 10) ? '0' . $month : $month;
    }

    /**
     *
     * @return type string
     */
    private function getDateFromInput($product_id) {
        $date_from = $this->getRequest()->get('date_from');
        if ($date_from != '') {
            return $this->getConvertDate($date_from, '-');
        } else {
            // check of quote
            if ($this->getQuoteDateFrom($product_id) != '') {
                return $this->getQuoteDateFrom($product_id)->format('d.m.Y');
            } else {
                return '';
            }
        }
    }

    /**
     *
     * @return type string
     */
    private function getDateToInput($product_id) {
        $date_to = $this->getRequest()->get('date_to');
        if ($date_to != '') {
            return $this->getConvertDate($date_to, '-');
        } else {
            if ($this->getQuoteDateTo($product_id) != '') {
                return $this->getQuoteDateTo($product_id)->format('d.m.Y');
            } else {
                return '';
            }
        }
    }

    /**
     *
     * @return type integer
     */
    private function getNowYear() {
        $date = new \DateTime('now');
        return $date->format('Y');
    }

    /**
     *
     * @return type string
     */
    private function getNowMonth() {
        $date = new \DateTime('now');
        return $date->format('m');
    }

    /**
     *
     * @return type integer
     */
    private function getCountDayWeek() {
        return $this->count_day_week;
    }

    /**
     *
     * @return type integer
     */
    private function getModuloWeek() {
        return ($this->getCountDayWeek() - ($this->getFirstWeekdayId() - 1)) % 7;
    }

    /**
     *
     * @return type integer
     */
    private function getBeginDays() {
        return ($this->getCountDayWeek() - $this->getModuloWeek()) % 7;
    }

    /**
     *
     * @return type integer
     */
    private function getLastDays() {
        return ($this->getCountDayWeek() - $this->getLastWeekdayId()) % 7;
    }

    /**
     *
     * @return type deliver_standard
     */
    private function getDeliverStandard() {
        return $this->em->getRepository(Standard::class)->findOneBy([]);
    }

    /**
     *
     * @return type deliver_exception
     */
    private function getDeliverException() {
        return $this->em->getRepository(Deliver::class)->findByMonthYear($this->getMonthCurrent(), $this->getYearCurrent(), $this->getMonthAfter(), $this->getYearAfter());
    }

    /**
     *
     * @return type string
     */
    public function getMonthName() {
        $date = new \DateTime($this->getYearCurrent() . '-' . $this->getMonthCurrent() . '-' . '01');
        $formatter = new \IntlDateFormatter($this->getRequest()->getLocale(), \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
        $formatter->setPattern("MMMM");
        return $formatter->format($date);
    }

    /**
     *
     * @return type integer
     */
    private function getBeginWeekdayId() {
        return $this->begin_weekday;
    }

    /**
     *
     * @return type days
     */
    public function getWeekDays() {
        $days = $this->getDays();
        // get weekname 6 begin as saturday
        $countWeekday = 0;
        $arr_weekday = array();
        foreach ($days as $day) {
            if ($day->getWeekdayId() == $this->getBeginWeekdayId() && $countWeekday == 0) {
                $countWeekday = 1;
            }
            if ($countWeekday > 0 && $countWeekday <= 7) {
                $arr_weekday[] = $day;
                $countWeekday++;
            }
        }
        return $arr_weekday;
    }

    /**
     *
     * @return string
     */
    public function getHtmlCalendar($product_id = '') {
        if ($product_id) {
            $this->setCheckCalendar($product_id);
        }
        $engine = $this->container->get('templating');
        return $engine->render('shop/calendar/index.html.twig'
                        , array(
                    'calendar' => $this,
                    'product_id' => $product_id,
                    'nowMonth' => $this->getNowMonth(),
                    'nowYear' => $this->getNowYear(),
                    'beginDays' => $this->getBeginDays(),
                    'lastDays' => $this->getLastDays(),
                    'countDayWeek' => $this->getCountDayWeek(),
                    'moduloWeek' => $this->getModuloWeek()
        ));
    }

    /**
     *
     * @return type string
     */
    public function getHtmlInputFromTo($product_id = '') {
        $engine = $this->container->get('templating');
        return $engine->render('shop/calendar/fromto.html.twig'
                        , array(
                    'product_id' => $product_id,
                    'input_from' => $this->getDateFromInput($product_id),
                    'input_to' => $this->getDateToInput($product_id),
        ));
    }

    /**
     * This of ajax request
     * @return type string
     */
    public function getCountDaysFromTo($product_id = '') {
        $engine = $this->container->get('templating');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $date = $this->getRequest()->get('dates');
            $date = json_decode($date);
            $date_from = $date->date_from;
            $date_to = $date->date_to;
            //$data_from = explode('.', $date->date_from);
            //$data_to = explode('.', $date->date_to);
        } else {
            $date_from = $this->getRequest()->get('date_from');
            $date_to = $this->getRequest()->get('date_to');
        }
        if ($this->getRequest()->isXmlHttpRequest()) {
            $product_id = $this->getRequest()->get('product');
        }
        $product = $this->em->getRepository(Product::class)->findOneById($product_id);

        if ($date_from == '' && $date_to == '' && !$this->container->get('session')->get('quote_id')) {
            return false;
        } else if ($date_from == '' && $date_to == '' && $this->container->get('session')->get('quote_id')) {
            $arr_additionals = $this->getQuoteAdditionals($product_id);
            $count_days = $this->getQuoteCountDays($product_id);
            if (!$arr_additionals && !$count_days) {
                return false;
            }
        } else {
            if ($this->getRequest()->isXmlHttpRequest()) {
                $date_from = new \DateTime($this->getConvertDate($date_from));
                $date_to = new \DateTime($this->getConvertDate($date_to));
            } else {
                $date_from = new \DateTime($date_from);
                $date_to = new \DateTime($date_to);
            }
            $interval = $date_from->diff($date_to);
            $count_days = $interval->format('%a');

            $additionals = $this->getRequest()->get('additionals');
            if ($this->getRequest()->isXmlHttpRequest()) {
                $additionals = json_decode($additionals);
            } else {
                $additionals = explode(',', $additionals);
            }
            $arr_additionals = array();
            foreach ($additionals as $additional) {
                if ($additional) {
                    $arr_additionals[] = $this->em->getRepository(Product::class)->findOneById($additional);
                }
            }
        }

        return $engine->render('shop/calendar/result.html.twig'
                        , array(
                    'product' => $product,
                    'additionals' => $arr_additionals,
                    'countdays' => $count_days,
        ));
    }

    /**
     *
     * @return type string
     */
    private function getQuoteDateFrom($product_id) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id) {
            $quote = $this->em->getRepository(Quote::class)->findOneById($quote_id);
            foreach ($quote->getCalendars() as $calendar) {
                //if ($calendar->getProduct()->getId() == $product_id) {
                return $calendar->getDateFrom();
                //}
            }
        }
        return '';
    }

    /**
     *
     * @param type $product_id
     * @return string
     */
    private function getQuoteDateTo($product_id) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id) {
            $quote = $this->em->getRepository(Quote::class)->findOneById($quote_id);
            foreach ($quote->getCalendars() as $calendar) {
                //if ($calendar->getProduct()->getId() == $product_id) {
                return $calendar->getDateTo();
                //}
            }
        }
        return '';
    }

    /**
     *
     * @param type $product_id
     * @return string
     */
    private function getQuoteAdditionals($product_id) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id) {
            $quote = $this->em->getRepository(Quote::class)->findOneById($quote_id);
            $additionals = $this->em->getRepository(QuoteProductAdditional::class)->findBy(array('quote' => $quote, 'parent' => $product_id));
            $arr_additionals = array();
            foreach ($additionals as $additional) {
                $arr_additionals[] = $this->em->getRepository(Product::class)->findOneById($additional->getChildren());
            }
            if (count($arr_additionals)) {
                return $arr_additionals;
            }
        }
        return '';
    }

    /**
     *
     * @param type $product_id
     * @return type integer
     */
    private function getQuoteCountDays($product_id) {
        if ($this->getQuoteDateFrom($product_id)) {
            $interval = $this->getQuoteDateFrom($product_id)->diff($this->getQuoteDateTo($product_id));
            return $interval->format('%a');
        } else {
            return '';
        }
    }

    /**
     *
     * @param type $product_id
     */
    public function setCheckCalendar($product_id) {
        $quote_id = $this->getCurrentQuoteId();
        $arr_products_quotes = $this->getIsRelationProduct($product_id, $quote_id);
        foreach ($arr_products_quotes as $product => $quote) {
            $this->setCrossing($product, $quote);
            $this->setCurrentCalendar($product, $quote);
        }
    }

    /**
     *
     * @return int $quote_id
     */
    private function getCurrentQuoteId() {
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id == '') {
            $quote_id = 0;
        }
        return $quote_id;
    }

    /**
     *
     * @param type $product_id
     */
    private function getIsRelationProduct($product_id, $quote_id) {
        $arr_products_quotes = array();
        $arr_products_quotes[$product_id] = $quote_id;
        $product = $this->em->getRepository(Product::class)->findOneById($product_id);
        if (strpos($product->getSku(), '_') !== FALSE) {
            $skus = explode('_', $product->getSku());
            $products = $this->em->getRepository(Product::class)->findRelationProducts($skus[0], $product->getSku());
            foreach ($products as $product) {
                $arr_products_quotes[$product->getId()] = 0;
            }
        }
        return $arr_products_quotes;
    }

    /**
     *
     * @param type $product_id
     */
    private function setCrossing($product_id, $quote_id) {
        $quotes = $this->getCurrentQuotes($quote_id);
        foreach ($quotes as $quote) {
            foreach ($quote->getCalendars() as $calendar) {
                if ($calendar->getProduct()->getId() == $product_id) {
                    $this->days_crossing['date_from'][$calendar->getDateFrom()->format('Y-m-d')] = $calendar->getDateFrom()->format('Y-m-d');
                    $this->days_crossing['date_to'][$calendar->getDateTo()->format('Y-m-d')] = $calendar->getDateTo()->format('Y-m-d');
                }
            }
        }
        $orders = $this->getCurrentOrders($product_id);
        foreach ($orders as $order) {
            foreach ($order->getCalendars() as $calendar) {
                $this->days_crossing['date_from'][$calendar->getDateFrom()->format('Y-m-d')] = $calendar->getDateFrom()->format('Y-m-d');
                $this->days_crossing['date_to'][$calendar->getDateTo()->format('Y-m-d')] = $calendar->getDateTo()->format('Y-m-d');
            }
        }
    }

    /**
     *
     * @return type $quotes
     */
    private function getCurrentQuotes($quote_id) {
        return $this->em->getRepository(Quote::class)->getCurrentQuotes($quote_id);
    }

    /**
     *
     * @param type $product_id
     * @return type $orders
     */
    private function getCurrentOrders($product_id) {
        $statutes = $this->em->getRepository(Status::class)->findAll();
        $arr_status = array();
        foreach ($statutes as $status) {
            if ($status->getName() != 'canceled') {
                $arr_status[] = $status->getName();
            }
        }
        $product = $this->em->getRepository(Product::class)->findOneById($product_id);
        return $this->em->getRepository(Order::class)->getOrderDatesAll($product, $this->getMonthBefore(), $this->getYearBefore(), $this->getCountBeforeDays(), $this->getMonthAfter(), $this->getYearAfter(), $this->getCountAfterDays(), $arr_status);
        //return $this->em->getRepository(Order::class)->getOrderDates($product, $this->getMonthCurrent(), $this->getYearCurrent(), $this->getCountDays(), $arr_status);
    }

    /**
     *
     * @param type $product_id
     */
    private function setCurrentCalendar($product_id, $quote_id) {
        // quote
        $quotes = $this->getCurrentQuotes($quote_id);
        foreach ($quotes as $quote) {
            foreach ($quote->getCalendars() as $calendar) {
                if ($calendar->getProduct()->getId() == $product_id) {
                    $this->setCalendarDatesFromTo($calendar->getDateFrom(), $calendar->getDateTo());
                }
            }
        }
        // order
        $orders = $this->getCurrentOrders($product_id);
        foreach ($orders as $order) {
            foreach ($order->getCalendars() as $calendar) {
                $this->setCalendarDatesFromTo($calendar->getDateFrom(), $calendar->getDateTo());
            }
        }

        // check if before and after are no deliver
    }

    /**
     *
     * @param type $dateFrom
     * @param type $dateTo
     * @return type $dates
     */
    public function getRequestCalendarDates($dates) {
        $isDate = FALSE;
        $arr_dates = array();
        $new_date = new \DateTime($this->getConvertDate($dates->date_from));
        while (!$isDate) {
            $new_date->add(new \DateInterval('P1D'));
            if ($new_date->format('Y-m-d') != $this->getConvertDate($dates->date_to)) {
                $arr_dates[] = $new_date->format('Y-m-d');
            } else {
                $isDate = TRUE;
            }
        }
        // check crossing
        if ($this->getIsCrossingFrom($dates->date_from)) {
            $arr_dates[] = $this->getConvertDate($dates->date_from);
        }
        if ($this->getIsCrossingTo($dates->date_to)) {
            $arr_dates[] = $this->getConvertDate($dates->date_to);
        }
        return $arr_dates;
    }

    /**
     *
     * @param type $dates
     * @return boolean
     */
    private function getIsCrossingFrom($dateFrom) {
        if (isset($this->days_crossing['date_to'][$this->getConvertDate($dateFrom)])) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param type $dates
     * @return boolean
     */
    private function getIsCrossingTo($dateTo) {
        if (isset($this->days_crossing['date_from'][$this->getConvertDate($dateTo)])) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param type $dateFrom
     * @param type $dateTo
     * @return type $dates
     */
    private function setCalendarDatesFromTo($dateFrom, $dateTo) {
        $isDate = false;
        $new_date = new \DateTime($dateFrom->format('Y-m-d'));
        if (isset($this->days_crossing['date_to'][$dateFrom->format('Y-m-d')])) {
            $this->days['day'][] = $new_date->format('Y-m-d');
        } else {
            $this->days['to'][] = $dateFrom->format('Y-m-d');
        }

        while (!$isDate) {
            $new_date->add(new \DateInterval('P1D'));
            if ($new_date->format('Y-m-d') != $dateTo->format('Y-m-d')) {
                $this->days['day'][] = $new_date->format('Y-m-d');
            } else {
                $isDate = true;
            }
        }
        if (isset($this->days_crossing['date_from'][$dateTo->format('Y-m-d')])) {
            $this->days['day'][] = $dateTo->format('Y-m-d');
        } else {
            $this->days['from'][] = $dateTo->format('Y-m-d');
        }
    }

    public function getDayBeforeAfterEmpty($day, $action) {
        $arr_reseved_days = array();
        $date = new \DateTime($day->format('Y-m-d'));
        if ($action == 'add') {
            $date->add(new \DateInterval('P1D'));
        } else {
            $date->sub(new \DateInterval('P1D'));
        }
        if (!$this->getDeliverStandard()->getMonday()) {
            if ($date->format('N') == '1' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getTuesday()) {
            if ($date->format('N') == '2' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getWednesday()) {
            if ($date->format('N') == '3' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getThursday()) {
            if ($date->format('N') == '4' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getFriday()) {
            if ($date->format('N') == '5' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getSaturday()) {
            if ($date->format('N') == '6' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        } else if (!$this->getDeliverStandard()->getSunday()) {
            if ($date->format('N') == '7' && !$this->getIsExceptionDay($date->format('Y-m-d'))) {
                return $day->format('Y-m-d');
            }
        }
        return false;
    }

    private function getIsExceptionDay($day) {
        $isException = false;
        foreach ($this->getDeliverException() as $deliver) {
            if ($day == $deliver->getDate()->format('Y-m-d')) {
                $isException = true;
            }
        }
        return $isException;
    }

    /**
     *
     * @return type array
     */
    public function getCurrentCalendar() {
        return $this->days;
    }

    /**
     *
     * @param type $date
     * @return type string
     */
    public function getConvertDate($date, $typ = '.') {
        if ($typ == '.') {
            $date = explode('.', $date);
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        }

        if ($typ == '-') {
            $date = explode('-', $date);
            return $date[2] . '.' . $date[1] . '.' . $date[0];
        }
    }

    /**
     *
     * @param type $product_id
     * @param type $quote_id
     * @return calendar
     */
    public function getIsMyCalendarReserved($quote, $product) {
        return $this->em->getRepository(\App\Entity\Calendar::class)->findOneBy(array('product' => $product, 'quote' => $quote));
    }

}
