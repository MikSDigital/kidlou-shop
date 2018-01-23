<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Quote;
use App\Entity\Order as EntityOrder;
use App\Entity\Order\Item;
use App\Entity\Order\Address as OrderAddress;
use App\Entity\Order\Payment as OrderPayment;
use App\Entity\Map\OrderProductAdditional;
use App\Entity\Map\OrderItemAdditional;
use App\Service\Payment\Typ\Post;
use App\Service\Payment\Typ\Paypal;
use App\Entity\User;
use App\Entity\User\Personal;
use App\Entity\Calendar As EntityCalendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class Order {

    /**
     *
     * @var string $shipping_type
     */
    private $shipping_typ = 'shipping';

    /**
     *
     * @var string $billing_type
     */
    private $billing_typ = 'billing';

    /**
     *
     * @var string $begin_order_number
     */
    private $begin_order_number = 'KID-100000300';

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var trequest
     */
    private $request;

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @var container $container
     */
    private $container;

    /**
     *
     * @var common
     */
    private $common;

    /**
     *
     * @var time $now_time
     */
    private $now_time;

    /**
     *
     * @var mailer $templating
     */
    private $templating;

    /**
     *
     * @var type $post
     */
    private $post;

    /**
     *
     * @var type $paypal
     */
    private $paypal;

    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param Container $container
     * @param \App\Service\Common $common
     *
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Container $container, Common $common) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->setCurrentRequest();
        $this->container = $container;
        $this->common = $common;
        $this->setNowTime();
    }

    /**
     *
     * @return EntityManager $em
     */
    private function getEm() {
        return $this->em;
    }

    /**
     * set request
     */
    private function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
//$this->request->getLocale()
    }

    /**
     *
     * @return request $request
     */
    private function getRequest() {
        return $this->request;
    }

    /**
     * set now time
     */
    private function setNowTime() {
        $this->now_time = new \DateTime();
    }

    /**
     *
     * @return time now_time
     */
    private function getNowTime() {
        return $this->now_time;
    }

    /**
     *
     * @return session $container
     */
    private function getContainer() {
        return $this->container;
    }

    /**
     *
     * @return session $container
     */
    private function getCommon() {
        return $this->common;
    }

    /**
     *
     * @return string shipping_typ
     */
    public function getShippingType() {
        return $this->shipping_typ;
    }

    /**
     *
     * @return string billing_typ
     */
    public function getBillingType() {
        return $this->billing_typ;
    }

    /**
     *
     * @return string $begin_order_number
     */
    public function getBeginOrderNumber() {
        return $this->begin_order_number;
    }

    /**
     *
     * @return type $string
     */
    public function getPaymentName() {
        $order = $this->getCurrentOrder();
        return $this->getEm()->getRepository(Order\Payment::class)->findOneBy(array('order' => $order))->getPaymentName();
    }

    /**
     * set post
     */
    public function setPost() {
        $this->post = new Post($this->getEm(), $this->getRequest(), $this->getContainer(), $this->getCommon());
        return $this;
    }

    /**
     *
     * @return type $post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * set paypal
     */
    public function setPaypal($paypal_order) {
        $this->paypal = new Paypal($this->getEm(), $this->getRequest(), $this->getContainer(), $this->getCommon(), $paypal_order);
        return $this;
    }

    /**
     *
     * @return type $post
     */
    public function getPaypal() {
        return $this->paypal;
    }

    /**
     *
     * @return quote $quote
     */
    private function getCurrentQuote() {
        $quote_id = $this->getContainer()->get('session')->get('quote_id');
        return $this->getEm()->getRepository(Quote::class)->findOneById($quote_id);
    }

    /**
     *
     * @return quote $quote
     */
    public function getCurrentOrder() {
        $order_id = $this->getContainer()->get('session')->get('order_id');
        return $this->getEm()->getRepository(Order::class)->findOneById($order_id);
    }

    /**
     *
     * @return status $status
     */
    private function getStatusByName($name) {
        return $this->getEm()->getRepository(Order\Status::class)->findOneBy(array('name' => $name));
    }

    /**
     *
     * @param type $name
     * @return type $statuses
     */
    public function getAllStatus() {
        return $this->getEm()->getRepository(Order\Status::class)->findAll();
    }

    /**
     *
     * @return string $ordernumber
     */
    private function _getOrderNumber() {
        $arr_order = array();
        $order_number = $this->getBeginOrderNumber();
        $order = $this->getEm()->getRepository(Order::class)
                ->findOneBy(array(), array('id' => 'DESC'));
        if ($order) {
            $order_number = $order->getOrderNumber();
            $arr_order = explode('-', $order_number);
            $order_number = intval($arr_order[1]) + 1;
            $order_number = 'KID-' . $order_number;
        }
        return $order_number;
    }

    /**
     * save order
     */
    public function save() {

        try {
            // check if user
            if (isset($this->getRequest()->get('billing')['user_new'])) {
                $this->saveUser();
            }
            // save order
            $this->saveOrder();
            // save order address
            $this->saveOrderAddress();
            // save order payment
            $this->saveOrderPayment();
            // save calendar
            $this->saveOrderCalendar();
            // save order additional
            //$this->saveOrderAdditional();
            // save order item
            $this->saveOrderItem();
            // save new user


            return $this;
        } catch (Exception $ex) {
            // Hier log schreiben
            // order status nach cancel setzen
            return false;
        }
    }

    /**
     * save order
     */
    private function saveOrder() {
        $lang = $this->getEm()->getRepository(Language::class)->findOneBy(array('short_name' => $this->getRequest()->getLocale()));
        $order = $this->getEm()->getRepository(Order::class)->findOneBy(array('quote' => $this->getCurrentQuote()));

        if (!$order) {
            $order = new EntityOrder();
            $order->setCreated(new \DateTime());
            $order->setOrderNumber($this->_getOrderNumber());
            $order->setQuote($this->getCurrentQuote());
            $order->setStatus($this->getStatusByName('pending'));
            $order->setLang($lang);
            $this->getEm()->persist($order);
            $this->getEm()->flush();
        }
        $this->saveOrderSession($order);
    }

    /**
     *
     * @param order $order
     */
    private function saveOrderSession($order) {
        //$this->getContainer()->get('session')->migrate($destroy = true, $lifetime = 3600);
        $this->getContainer()->get('session')->set('order_id', $order->getId());
    }

    /**
     *
     * @param type $order
     */
    public function removeOrderSession() {
        $this->getContainer()->get('session')->remove('order_id');
    }

    /**
     *
     * @param type $order
     */
    public function removeQuoteSession() {
        $this->getContainer()->get('session')->remove('quote_id');
    }

    /**
     *
     * @param type $order
     */
    public function removeBasketItemsSession() {
        $this->getContainer()->get('session')->remove('basket_items');
        $this->getContainer()->get('session')->remove('cash_cost');
        $this->getContainer()->get('session')->remove('price_subtotal');
    }

    /**
     * save address Order
     */
    private function saveOrderAddress() {
        $addressTyp = $this->getEm()->getRepository(Order\Address::class)->findOneBy(array('order' => $this->getCurrentOrder(), 'address_typ' => $this->getBillingType()));
// save billing
        if (!$addressTyp) {
            $addressTyp = new OrderAddress();
            $addressTyp->setCreated($this->getNowTime());
            $addressTyp->setOrder($this->getCurrentOrder());
        }
        $this->_saveAddress($addressTyp, 'billing');

// save shipping
        $addressTyp = $this->getEm()->getRepository(Order\Address::class)->findOneBy(array('order' => $this->getCurrentOrder(), 'address_typ' => $this->getShippingType()));
        if (!$addressTyp) {
            $addressTyp = new OrderAddress();
            $addressTyp->setCreated($this->getNowTime());
            $addressTyp->setOrder($this->getCurrentOrder());
        }
        $this->_saveAddress($addressTyp, 'shipping');
    }

    /**
     * save Billing
     */
    private function _saveAddress($object, $typ) {
        $data = $this->getRequest()->get($typ);
        if ($typ == 'billing') {
            $object->setAddressTyp($this->getBillingType());
        } else {
            $object->setAddressTyp($this->getShippingType());
        }
        $object->setFirstName($data['firstname']);
        $object->setLastName($data['lastname']);
        if ($typ == 'billing') {
            $object->setEmail($data['email']);
        }
        $object->setPhone($data['phone']);
        $street = $data['street1'];
        if ($data['street1'] != '') {
            $street = $data['street1'] . ' ' . $data['street2'];
        }

        if ($this->getContainer()->get("security.token_storage")->getToken()) {
            $object->setUser($this->getContainer()->get("security.token_storage")->getToken()->getUser());
        }
        $object->setStreet($street);
        $object->setPostCode($data['post_code']);
        $object->setCity($data['city']);
        $object->setCountryCode($data['country_code']);
        $object->setUpdated($this->getNowTime());
        $this->getEm()->persist($object);
        $this->getEm()->flush();
    }

    /**
     * save order payment
     */
    private function saveOrderPayment() {
        $paymenttyp_id = $this->getRequest()->get('paymenttyp');
        $paymenttyp = $this->getEm()->getRepository(Payment::class)->findOneById($paymenttyp_id);
        $paymentAdditional = json_encode($this->getInstitutPaymentAsArray($paymenttyp->getName()));
        $orderPayment = $this->getEm()->getRepository(Order\Payment::class)->findOneBy(array('order' => $this->getCurrentOrder()));
        if (!$orderPayment) {
            $orderPayment = new OrderPayment();
        }
        $orderPayment->setSubtotalCost($this->getContainer()->get('session')->get('price_subtotal'));
        $orderPayment->setShippingCost($this->getCommon()->getShippingCost());
        $orderPayment->setCautionCost($this->getCommon()->getCautionCost());
        $orderPayment->setCashCost($this->getContainer()->get('session')->get('cash_cost'));
        $orderPayment->setAmountSubtotalCost(0);
        $orderPayment->setAmountShippingCost(0);
        $orderPayment->setPaymentAdditionalInformation($paymentAdditional);
        $orderPayment->setOrder($this->getCurrentOrder());
        $orderPayment->setPaymentName($paymenttyp->getName());
        $this->getEm()->persist($orderPayment);
        $this->getEm()->flush();
    }

    /**
     * save order calendar
     */
    private function saveOrderCalendar() {
        $order = $this->getCurrentOrder();
        $calendars = $this->getEm()->getRepository(Calendar::class)->findBy(array('quote' => $this->getCurrentQuote()));
        foreach ($calendars as $calendar) {
            $calendar->setOrder($order);
            $this->getEm()->persist($calendar);
            $this->getEm()->flush();
        }
    }

    /**
     * save order calendar additional
     */
    private function saveOrderAdditional() {
        $order = $this->getCurrentOrder();
        $quoteAdditionals = $this->getEm()->getRepository(Map\QuoteProductAdditional::class)->findBy(array('quote' => $this->getCurrentQuote()));

        // delete order map if exists
        $orderAdditionals = $this->getEm()->getRepository(Map\OrderProductAdditional::class)
                ->findBy(array('order' => $order));

        foreach ($orderAdditionals as $orderAdditional) {
            $this->getEm()->remove($orderAdditional);
            $this->getEm()->flush();
        }

        foreach ($quoteAdditionals as $quoteAdditional) {
            $orderAdditional = new OrderProductAdditional();
            $orderAdditional->setOrder($order);
            $orderAdditional->setChildren($quoteAdditional->getChildren());
            $orderAdditional->setParent($quoteAdditional->getParent());
            $this->getEm()->persist($orderAdditional);
            $this->getEm()->flush();
        }
    }

    /**
     * save order item
     */
    public function saveOrderItem() {
        $order = $this->getCurrentOrder();
        $calendars = $this->getEm()->getRepository(Calendar::class)
                ->findBy(array('order' => $order));
        // check if order item exist if yes then remove
        $arr_order_item_additionals = array();
        foreach ($calendars as $calendar) {
            $items = $this->getEm()->getRepository(Order\Item::class)
                    ->findBy(array('calendar' => $calendar));
            foreach ($items as $item) {
                $arr_order_item_additionals[] = $item;
                $this->getEm()->remove($item);
                $this->getEm()->flush();
                // remove from order order item
            }
        }
        // delete orderItemAdditional
        foreach ($arr_order_item_additionals as $item_additional) {
            $orderItemAdditionals = $this->getEm()->getRepository(Map\OrderItemAdditional::class)->findBy(array('parent' => $item_additional->getId()));
            foreach ($orderItemAdditionals as $orderItemAdditional) {
                $this->getEm()->remove($orderItemAdditional);
                $this->getEm()->flush();
            }
        }

        $arr_order_item_additionals = array();
        $arr_order_product_additionals = array();
        $parent = 0;
// add order to calendar data
        foreach ($calendars as $calendar) {
            $item = new Item();
            $item->setCalendar($calendar);
            $item->setName($this->_getProductItemName($calendar->getProduct()));
            $item->setPrice($calendar->getProduct()->getPrice()->getValue());
            $item->setSku($calendar->getProduct()->getSku());
            $item->setProductId($calendar->getProduct()->getId());
            $this->getEm()->persist($item);
            $this->getEm()->flush();
            $parent = $item;


            // check if has children
            $quoteAdditionals = $this->getEm()->getRepository(Map\QuoteProductAdditional::class)
                    ->findBy(array('quote' => $this->getCurrentQuote(), 'parent' => $calendar->getProduct()->getId()));
            foreach ($quoteAdditionals as $quoteAdditional) {
                $item = new Item();
                $item->setCalendar($calendar);
                $children = $this->getEm()->getRepository(Product::class)->findOneById($quoteAdditional->getChildren());
                $item->setName($this->_getProductItemName($children));
                $item->setPrice($children->getPrice()->getValue());
                $item->setSku($children->getSku());
                $item->setProductId($children->getId());
                $this->getEm()->persist($item);
                $this->getEm()->flush();
                $arr_order_item_additionals[$item->getId()] = $parent->getId();
                $arr_order_product_additionals[$item->getId()][$children->getId()] = $calendar->getProduct()->getId();
            }
        }

        // order item additional
        foreach ($arr_order_item_additionals as $children => $parent) {
            $item = new OrderItemAdditional();
            $item->setParent($parent);
            $item->setChildren($children);
            foreach ($arr_order_product_additionals[$children] as $p_children => $p_parent) {
                $item->setParentProduct($p_parent);
                $item->setChildrenProduct($p_children);
            }
            $this->getEm()->persist($item);
            $this->getEm()->flush();
        }
    }

    /**
     *
     * @param product $product
     * @return string product_name
     */
    private function _getProductItemName($product) {
        foreach ($product->getDescriptions() as $description) {
            if ($description->getLang() != NULL) {
                if ($description->getLang()->getShortName() == $this->getCurrentOrder()->getLang()->getShortName()) {
                    return $description->getName();
                }
            }
        }
    }

    /**
     *
     * @return array bank_payment
     */
    public function getInstitutPaymentAsArray($name) {
        return $this->getEm()
                        ->getRepository('Payment\\' . $name . '::class')
                        ->createQueryBuilder('p')
                        ->select('p')
                        ->getQuery()
                        ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     *
     * @return array payment_additional_information
     */
    private function getPaymentAdditionalInformation() {
        $orderPayment = $this->getEm()->getRepository(Order\Payment::class)->findOneBy(array('order' => $this->getCurrentOrder()));
        return (array) json_decode($orderPayment->getPaymentAdditionalInformation());
    }

    /**
     *
     * @return array $url
     */
    public function getUrlInstitut() {
        $arr_payment_add_information = $this->getPaymentAdditionalInformation();
        return $arr_payment_add_information['formular_url'];
    }

    /**
     *
     * @return type paymenttyp
     */
    public function getPaymenttyp() {
        return $this->getEm()->getRepository(Order\Payment::class)->findOneBy(array('order' => $this->getCurrentOrder()));
    }

    /**
     *
     * @return string url
     */
    public function getUrl() {
// check if url is intern, if has protocol then external link, if no protocol then internal link
        $_protocol = 'http://';
        $request = $this->getRequest();
        if ($request->isSecure()) {
            $_protocol = 'https://';
        }
        $url = $this->getUrlInstitut();
        if (strpos($this->getUrlInstitut(), 'http') === FALSE) {
            $url = $_protocol . $request->getHost() . '/' . $request->getLocale() . $this->getUrlInstitut();
        }
        return $url;
    }

    /**
     * canceled order
     */
    public function setOrderStatus($statustyp) {
        $order = $this->getEm()->getRepository(Order::class)->findOneById($this->getCurrentOrder()->getId());
        if ($order) {
            $order->setStatus($this->getStatusByName($statustyp));
            $this->getEm()->persist($order);
            $this->getEm()->flush();
        }
    }

    /**
     * save additional Order
     */
    public function setAdditionalInformation($arr_data) {
        $orderPayment = $this->getEm()->getRepository(Order\Payment::class)->findOneBy(array('order' => $this->getCurrentOrder()));
        if ($orderPayment) {
            $orderPayment->setAdditionalInformation(json_encode($arr_data));
            $this->getEm()->persist($orderPayment);
            $this->getEm()->flush();
        }
    }

    /** MAIL PART * */

    /**
     *
     * @return string name
     */
    public function getName() {
        $orderAddress = $this->getEm()->getRepository(Order\Address::class)->findOneBy(array('order' => $this->getCurrentOrder(), 'address_typ' => $this->getBillingType()));
        return $orderAddress->getFirstName() . ' ' . $orderAddress->getLastname();
    }

    /**
     *
     * @return string $number
     */
    public function getOrderNumber() {
        $order = $this->getEm()->getRepository(Order::class)->findOneBy(array('order' => $this->getCurrentOrder()));
        return $order->getOrderNumber();
    }

    /**
     *
     * @return string $number
     */
    public function getOrderCreated() {
        $order = $this->getEm()->getRepository(Order::class)->findOneBy(array('order' => $this->getCurrentOrder()));
        return $order->getCreated()->format('d-m-Y H:i:s');
    }

    /**
     *
     * @return string $mail
     */
    public function getKidlouMail() {
        return 'info@kidlou.com';
    }

    /**
     *
     * @param string $html
     */
    public function sendEmailMessage($order, $mailer) {
        $order = $this->getOrderDataById($order->getId());
        $engine = $this->container->get('templating');
        $mail = $this->getEm()->getRepository(Mail::class)->findOneBy(array('status' => TRUE, 'type' => 'product'));
        $html = $engine->render('email/' . $this->getRequest()->getLocale() . '_' . strtolower($order['payment_name']) . '.html.twig', array('order' => $order, 'mail' => $mail));
        $arr_bcc_mails = explode(',', $mail->getBccEmail());
        $message = (new \Swift_Message($order['payment_name']))
                ->setFrom([$mail->getFromEmail() => $mail->getFromName()])
                ->setBcc($arr_bcc_mails)
                ->setTo($order['billing']['email'])
                ->setBody($html, 'text/html');
        return $mailer->send($message);
    }

    public function getOrderData($product) {
        return $this->getEm()->getRepository(EntityCalendar::class)->getReservedDates($product
                        , $this->getMonthCurrent(), $this->getYearCurrent()
                        , $this->getMonthBefore(), $this->getYearBefore()
                        , $this->getMonthNext(), $this->getYearNext());
    }

    /**
     *
     * @return integer $month
     */
    private function getMonthCurrent() {
        $month = (int) $this->getRequest()->get('month');
        if ($month == '') {
            $date = new \DateTime('now');
            $month = $date->format('m');
        }
        $month = (int) $month;
        return ($month < 10) ? '0' . $month : $month;
    }

    /**
     *
     * @return integer $month
     */
    private function getMonthBefore() {
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
     * @return integer $month
     */
    private function getMonthNext() {
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
     * @return integer $year
     */
    private function getYearCurrent() {
        $year = $this->getRequest()->get('year');
        if ($year == '') {
            $date = new \DateTime('now');
            $year = $date->format('Y');
        }
        return $year;
    }

    /**
     *
     * @return integer $year
     */
    private function getYearBefore() {
        $year = $this->getRequest()->get('year');
        if ($year == '') {
            $date = new \DateTime('now');
            $year = $date->format('Y');
        }
        $month = (int) $this->getMonthCurrent();
        if ($month == 1) {
            $year--;
        }
        return $year;
    }

    /**
     *
     * @return integer $year
     */
    private function getYearNext() {
        $year = (int) $this->getRequest()->get('year');
        if ($year == '') {
            $date = new \DateTime('now');
            $year = $date->format('Y');
        }
        $month = (int) $this->getMonthCurrent();
        if ($month == 12) {
            $year++;
        }

        return $year;
    }

    /**
     *
     * @param type $id
     * @return type $array order data
     */
    public function getOrderDataById($id) {
        $arr_order = $this->getEm()->getRepository(Order::class)->getOrderData($id);
//        print_r($arr_order);
//        exit;
        if ($arr_order['status'] != 'canceled') {
            $arr_order['additional_information'] = (array) json_decode($arr_order['additional_information']);
        }
        $arr_data = explode(',', $arr_order['address_name']);
        $arr_address_typ = array('billing', 'shipping');
        foreach ($arr_address_typ as $adress_typ) {
            foreach ($arr_data as $data) {
                $order_data = explode('|', $data);
                $isFound = 0;

                if (isset($order_data[8]) && $order_data[8] == $adress_typ && $isFound == 0) {
                    $isFound = 1;
                    $arr_order[$adress_typ]['name'] = $order_data[0];
                    $arr_order[$adress_typ]['street'] = $order_data[1];
                    $arr_order[$adress_typ]['postcode'] = $order_data[2];
                    $arr_order[$adress_typ]['city'] = $order_data[3];
                    $arr_order[$adress_typ]['country'] = $order_data[4];
                    $arr_order[$adress_typ]['phone'] = $order_data[5];
                    $arr_order[$adress_typ]['mobile'] = $order_data[6];
                    $arr_order[$adress_typ]['email'] = $order_data[7];
                }
            }
        }
        $arr_data = explode(',', $arr_order['item']);
        $arr_date = array();
        foreach ($arr_data as $datas) {
            $data = explode('|', $datas);
            // prüfe ob parent product
            if (!isset($data[7])) {
                continue;
            }
            if ($data[0] == $data[7]) {
                if (!isset($arr_date[$data[7]]['date_next'])) {
                    $arr_date[$data[7]]['date_from'] = $data[2];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['sku'] = $data[6];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['name'] = $data[5];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['date_to'] = $data[3];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['date_back_deliver'] = $data[3];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['price'] = $data[8];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['subtotal_price'] = $data[8] * $data[4];
                    $arr_date[$data[7]]['date_next'] = $this->getNextDate($data);
                }

                // prüfe ob datum bereits gesetzt ist
                if ($arr_date[$data[7]]['date_next'] == $data[2]) {
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['price'] = $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['price'] + $data[8];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['date_to'] = $data[3];
                    $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['date_back_deliver'] = $data[3];
                    $arr_date[$data[7]]['date_next'] = $this->getNextDate($data);
                } else {
                    // check if wiederholen
                    if (!$arr_order['item_data'][$arr_date[$data[7]]['date_from']]) {
                        $arr_date[$data[7]]['date_from'] = $data[2];
                        $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['sku'] = $data[6];
                        $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['name'] = $data[5];
                        $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['price'] = $data[8];
                        $arr_order['item_data'][$arr_date[$data[7]]['date_from']][$data[7]]['subtotal_price'] = $data[8] * $data[4];
                        $arr_date[$data[7]]['date_next'] = $this->getNextDate($data);
                    }
                }
            } else if ($data[0] != $data[7]) {
                $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['sku'] = $data[6];
                $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['name'] = $data[5];
                if (!isset($arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['price'])) {
                    $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['price'] = $data[8];
                    $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['subtotal_price'] = $data[8] * $data[4];
                } else {
                    $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['price'] = $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['price'] + $data[8];
                    $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['subtotal_price'] = $arr_order['item_data'][$arr_date[$data[0]]['date_from']][$data[0]]['children'][$data[7]]['subtotal_price'] + ($data[8] * $data[4]);
                }
            }
        }
        return $arr_order;
    }

    /**
     *
     * @param type $data
     * @return type date
     */
    private function getNextDate($data) {
        $date = new \DateTime($data[2]);
        if ($data[4] > 1) {
            $date->add(new \DateInterval('P' . $data[4] . 'D'));
        } else {
            $date->add(new \DateInterval('P1D'));
        }
        return $date->format('d-m-Y');
    }

    /**
     *
     * @param type $data
     * @return type date
     */
    private function getDateBackDeliver($date_deliver) {
        $date = new \DateTime($date_deliver);
        $date->add(new \DateInterval('P1D'));
        return $date->format('d-m-Y');
    }

    /**
     *
     * @param type $data
     * @return type date
     */
    private function getBeforeDate($data) {
        $date = new \DateTime($data[2]);
        if ($data[4] > 1) {
            $date->sub(new \DateInterval('P' . $data[4] . 'D'));
        } else {
            $date->sub(new \DateInterval('P1D'));
        }
        return $date->format('d-m-Y');
    }

    /**
     *
     * @return RedirectResponse
     */
    private function saveUser() {
        $data = $this->getRequest()->get('billing');
        $user = new User();
        $user->setUsername($data['email']);
        $user->setEmail($data['email']);
        $user->setIsActive(TRUE);
        $user->setPassword(password_hash($data['password1'], PASSWORD_BCRYPT));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        try {
            $this->getEm()->persist($user);
            $this->getEm()->flush();
        } catch (UniqueConstraintViolationException $e) {
            $this->getContainer()->get('session')->getFlashBag()->add('error', $this->getContainer()->get('translator')->trans('Username already exists'));
            return new RedirectResponse('checkout_cart');
        }

        $token = new UsernamePasswordToken($user, null, 'frontendlogin', $user->getRoles());
        $this->getContainer()->get('security.token_storage')->setToken($token);
        $this->getContainer()->get('session')->set('_security_frontendlogin', serialize($token));
//        $event = new InteractiveLoginEvent($this->getRequest(), $token);
//        $this->getContainer()->get('event_dispatcher')->dispatch('security.interactive_login', $event);

        $lang = $this->getEm()->getRepository(Language::class)->findOneBy(array('short_name' => $this->getRequest()->getLocale()));
        $person = new Personal();
        $person->setFirstName($data['firstname']);
        $person->setLastName($data['lastname']);
        $person->setStreet($data['street1'] . ' ' . $data['street2']);
        $person->setPostCode($data['post_code']);
        $person->setCity($data['city']);
        $person->setCountryCode($data['country_code']);
        $person->setPhone($data['phone']);
        $person->setMobile($data['phone']);
        $person->setStandard(TRUE);
        $person->setLang($lang);
        $person->setUser($user);
        try {
            $this->getEm()->persist($person);
            $this->getEm()->flush();
        } catch (Exception $ex) {

        }
    }

}
