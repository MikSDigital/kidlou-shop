<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Closas\ShopBundle\Entity\Quote;
use Closas\ShopBundle\Entity\Calendar;
use Closas\ShopBundle\Entity\Map\QuoteProductAdditional;
use Closas\ShopBundle\Helper\Calendar As HelperCalendar;
use Closas\ShopBundle\Helper\Common As HelperCommon;
use Closas\ShopBundle\Helper\Cart As HelperCart;

/**
 * @Route("/cart")
 */
class CartController extends Controller {

    /**
     * @Template("ClosasShopBundle/Cart/index.html.twig")
     * @Route("/index/", name="index_cart")
     */
    public function indexAction(Request $request) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle/Cart/empty.html.twig');
        }
        $basket_items = $this->container->get('session')->get('basket_items');
//        $basket = $this->get('helper.common')->setBasket();
//        $basket_items = $basket->getBasketItems();
        return array('items' => $basket_items);
    }

    /**
     * @Template()
     * @Route("/add/{id}", name="add_cart")
     */
    public function addAction($id, Request $request, HelperCalendar $helperCalendar, HelperCart $helperCart, HelperCommon $helperCommon) {
        $em = $this->getDoctrine()->getManager();
        $carttime = new \DateTime();
        $quote_id = 0;
        if (!$this->container->get('session')->get('quote_id')) {
            // hier eine quote erstellen
            $quote = new Quote();
            $quote->setCreated($carttime);
            $quote->setUpdated($carttime);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quote);
            $em->flush();
            //$this->container->get('session')->migrate(true, $lifetime = 3600);
            $this->container->get('session')->set('quote_id', $quote->getId());
            $quote_id = $quote->getId();
        } else {
            $quote_id = $this->container->get('session')->get('quote_id');
//            $this->container->get('session')->remove('quote_id');
//            $this->container->get('session')->set('quote_id', $quote_id);
            $quote = $this->getDoctrine()->getRepository('ClosasShopBundle:Quote')->findOneBy(array('id' => $quote_id));
        }

        $product = $helperCart->getProduct($id);
        $dates = $request->request->get('dates');
        $dates = json_decode($dates);
        $date_from = new \DateTime($helperCalendar->getConvertDate($dates->date_from));
        $date_to = new \DateTime($helperCalendar->getConvertDate($dates->date_to));
        $interval = $date_from->diff($date_to);
        $count_days = $interval->format('%a');

        $additionals = $request->request->get('additionals');
        $additionals = json_decode($additionals);
        $arr_additionals = array();
        foreach ($additionals as $additional) {
            $arr_additionals[] = $additional;
        }

        // check ob product im warenkorb liegen
        if ($this->getProductIsInBasket($product, $dates, $helperCalendar)) {
            $html = $this->renderView('ClosasShopBundle/Cart/productinbasket.html.twig'
                    , array(
                'date_from' => $dates->date_from,
                'date_to' => $dates->date_to,
                'product' => $product,
                    )
            );
            return new JsonResponse(array('html' => $html));
        }
        //         //
        // write in calendar
        $reposCalendar = $this->getDoctrine()->getRepository('ClosasShopBundle:Calendar');
        $calendar = $reposCalendar->findOneBy(
                array(
                    'quote' => $quote,
                    'product' => $product,
//                    'date_from' => $date_from,
//                    'date_to' => $date_to
                )
        );
        if (!$calendar) {
            $calendar = new Calendar();
        }
        $calendar->setQuote($quote);
        $calendar->setProduct($product);
        $calendar->setDateFrom($date_from);
        $calendar->setDateTo($date_to);
        $calendar->setOrder(NULL);
        $em->persist($calendar);
        $em->flush();

        // write new date in all calendar dates
        foreach ($quote->getCalendars() as $calendar) {
            $calendar->setDateFrom($date_from);
            $calendar->setDateTo($date_to);
            $em->persist($calendar);
            $em->flush();
        }

        // remove all from parent
        $quoteProductAdditionals = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional')->findBy(
                array(
                    'parent' => $product->getId(),
                    'quote' => $quote
        ));
        foreach ($quoteProductAdditionals as $quoteProductAdditional) {
            if ($quoteProductAdditional) {
                $em->remove($quoteProductAdditional);
                $em->flush();
            }
        }

        // This part is with by additional products
        // remove old additional

        $reposQuoteProductAdditional = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional');
        foreach ($arr_additionals as $additional) {
            $quoteProductAdditional = $reposQuoteProductAdditional->findOneBy(
                    array(
                        'parent' => $product->getId(),
                        'children' => $additional,
                        'quote' => $quote
            ));
            if (!$quoteProductAdditional) {
                $quoteProductAdditional = new QuoteProductAdditional();
            }
            $quoteProductAdditional->setParent($product->getId());
            $quoteProductAdditional->setChildren($additional);
            $quoteProductAdditional->setQuote($quote);
            $em->persist($quoteProductAdditional);
            $em->flush();
        }
        $additionals = array();
        foreach ($arr_additionals as $additional) {
            $additionals[] = $em->getRepository('ClosasShopBundle:Product')->findOneById($additional);
        }

// write in session for warenkorb
        $basket = $helperCommon->setBasket();
        $this->container->get('session')->set('basket_items', $basket->getBasketItems());
        $this->container->get('session')->set('price_subtotal', $basket->getSubtotal());

        $html = $this->renderView('ClosasShopBundle/Cart/add.html.twig'
                , array(
            'date_from' => $dates->date_from,
            'date_to' => $dates->date_to,
            'count_days' => $count_days,
            'product' => $product,
            'additionals' => $additionals,
            'id' => $id
                )
        );
        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/remove/", name="remove_cart_all")
     * @Route("/remove/{id}/", defaults={"id" = ""}, name="remove_cart")
     * @Route("/remove/{id}/{additional_id}", defaults={"additional_id" = ""}, name="remove_cart")
     */
    public function removeAction($id = "", $additional_id = "") {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle/Cart/empty.html.twig');
        }

        $reposQuote = $this->getDoctrine()->getRepository('ClosasShopBundle:Quote');
        $quote = $reposQuote->findOneBy(array('id' => $quote_id));
        $em = $this->getDoctrine()->getManager();
        if ($id != '') {
            $product = $this->get('helper.product')->getProduct($id);
            if ($additional_id) {
                $additional = $this->get('helper.product')->getProduct($additional_id);
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional');
                $quoteProductAdditional = $reposQuoteProductAdditional->findOneBy(
                        array(
                            'parent' => $product->getId(),
                            'children' => $additional->getId(),
                            'quote' => $quote
                ));

                $em->remove($quoteProductAdditional);
                $em->flush();
            } else {
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional');
                $quoteProductAdditionals = $reposQuoteProductAdditional->findBy(
                        array(
                            'parent' => $product->getId(),
                            'quote' => $quote
                ));

                foreach ($quoteProductAdditionals as $quoteProductAdditional) {
                    $em->remove($quoteProductAdditional);
                    $em->flush();
                }

                $reposQuoteCalendar = $this->getDoctrine()->getRepository('ClosasShopBundle:Calendar');
                $quoteCalendars = $reposQuoteCalendar->findBy(
                        array(
                            'product' => $product,
                            'quote' => $quote
                ));

                foreach ($quoteCalendars as $quoteCalendar) {
                    $em->remove($quoteCalendar);
                    $em->flush();
                }
            }
        } else {
            $reposQuoteCalendar = $this->getDoctrine()->getRepository('ClosasShopBundle:Calendar');
            $quoteCalendars = $reposQuoteCalendar->findBy(
                    array(
                        'quote' => $quote
            ));
            foreach ($quoteCalendars as $quoteCalendar) {
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional');
                $quoteProductAdditionals = $reposQuoteProductAdditional->findBy(
                        array(
                            'parent' => $quoteCalendar->getProduct()->getId(),
                            'quote' => $quote
                ));
                foreach ($quoteProductAdditionals as $quoteProductAdditional) {
                    $em->remove($quoteProductAdditional);
                    $em->flush();
                }
                $em->remove($quoteCalendar);
                $em->flush();
            }
        }
        $basket = $this->get('helper.common')->setBasket();
        $this->container->get('session')->set('basket_items', $basket->getBasketItems());
        $this->container->get('session')->set('price_subtotal', $basket->getSubtotal());
        return $this->redirectToRoute('index_cart');
    }

    /**
     *
     * @param Request $request
     * @return type ControllerTrait
     */
    public function couponAction(Request $request) {
        $this->request->get('coupons');
        return $this->redirectToRoute('index_cart');
    }

    /**
     *
     * @param type $product
     * @param type $quote_id
     * @param type $dates
     */
    private function getProductIsInBasket($product, $dates, $helperCalendar) {
        $helperCalendar->setCheckCalendar($product->getId());
        // check if first or last day is reserved
        $request_dates = $helperCalendar->getRequestCalendarDates($dates);
        $current_dates = $helperCalendar->getCurrentCalendar();
        foreach ($request_dates as $request_date) {
            foreach ($current_dates as $current_date) {
                if ($request_date == $current_date) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

}
