<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Quote;
use App\Entity\Calendar;
use App\Entity\Map\QuoteProductAdditional;
use App\Entity\Product;
use App\Service\Calendar As ServiceCalendar;
use App\Service\Common As ServiceCommon;
use App\Service\Cart As ServiceCart;
use App\Service\Product As ServiceProduct;

/**
 * @Route("/cart")
 */
class CartController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="index_cart")
     */
    public function indexAction(Request $request) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
        }
        $basket_items = $this->container->get('session')->get('basket_items');
        return array('items' => $basket_items);
    }

    /**
     * @Template()
     * @Route("/add/{id}", name="add_cart")
     */
    public function addAction($id, Request $request, ServiceCalendar $serviceCalendar, ServiceCart $serviceCart, ServiceCommon $serviceCommon) {
        $em = $this->getDoctrine()->getManager();
        $carttime = new \DateTime();
        $quote_id = 0;
        $em = $this->getDoctrine()->getManager();
        if (!$this->container->get('session')->get('quote_id')) {
            // hier eine quote erstellen
            $quote = new Quote();
            $quote->setCreated($carttime);
            $quote->setUpdated($carttime);
            $em->persist($quote);
            $em->flush();
            //$this->container->get('session')->migrate(true, $lifetime = 3600);
            $this->container->get('session')->set('quote_id', $quote->getId());
            $quote_id = $quote->getId();
        } else {
            $quote_id = $this->container->get('session')->get('quote_id');
//            $this->container->get('session')->remove('quote_id');
//            $this->container->get('session')->set('quote_id', $quote_id);
            $quote = $this->getDoctrine()->getRepository(Quote::class)->findOneBy(array('id' => $quote_id));
            $quote->setUpdated($carttime);
            $em->persist($quote);
            $em->flush();
        }

        $product = $serviceCart->getProduct($id);
        $dates = $request->request->get('dates');
        $dates = json_decode($dates);
        $date_from = new \DateTime($serviceCalendar->getConvertDate($dates->date_from));
        $date_to = new \DateTime($serviceCalendar->getConvertDate($dates->date_to));
        $interval = $date_from->diff($date_to);
        $count_days = $interval->format('%a');
        $additionals = $request->request->get('additionals');
        $additionals = json_decode($additionals);
        $arr_additionals = array();
        foreach ($additionals as $additional) {
            $arr_additionals[] = $additional;
        }

        // delete calendar eintrag für dieses product
        // check ob product im warenkorb liegen
        if ($this->getProductIsInBasket($quote, $product, $dates, $serviceCalendar)) {
            $html = $this->renderView('shop/cart/productinbasket.html.twig'
                    , array(
                'date_from' => $dates->date_from,
                'date_to' => $dates->date_to,
                'product' => $product,
                    )
            );
            return new JsonResponse(array('html' => $html));
        }
        // write in calendar
        $reposCalendar = $this->getDoctrine()->getRepository(Calendar::class);
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
        $quoteProductAdditionals = $this->getDoctrine()->getRepository(QuoteProductAdditional::class)->findBy(
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

        $reposQuoteProductAdditional = $this->getDoctrine()->getRepository(QuoteProductAdditional::class);
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
            $additionals[] = $em->getRepository(Product::class)->findOneById($additional);
        }

        // write in session for warenkorb
        $basket = $serviceCommon->setBasket();
        $html = $this->renderView('shop/cart/add.html.twig'
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
    public function removeAction($id = "", $additional_id = "", ServiceProduct $serviceProduct, ServiceCommon $serviceCommon) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
        }

        $reposQuote = $this->getDoctrine()->getRepository(Quote::class);
        $quote = $reposQuote->findOneBy(array('id' => $quote_id));
        $em = $this->getDoctrine()->getManager();
        if ($id != '') {
            $product = $serviceProduct->getProduct($id);
            if ($additional_id) {
                $additional = $serviceProduct->getProduct($additional_id);
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository(QuoteProductAdditional::class);
                $quoteProductAdditional = $reposQuoteProductAdditional->findOneBy(
                        array(
                            'parent' => $product->getId(),
                            'children' => $additional->getId(),
                            'quote' => $quote
                ));

                $em->remove($quoteProductAdditional);
                $em->flush();
            } else {
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository(QuoteProductAdditional::class);
                $quoteProductAdditionals = $reposQuoteProductAdditional->findBy(
                        array(
                            'parent' => $product->getId(),
                            'quote' => $quote
                ));

                foreach ($quoteProductAdditionals as $quoteProductAdditional) {
                    $em->remove($quoteProductAdditional);
                    $em->flush();
                }

                $reposQuoteCalendar = $this->getDoctrine()->getRepository(Calendar::class);
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
            $reposQuoteCalendar = $this->getDoctrine()->getRepository(Calendar::class);
            $quoteCalendars = $reposQuoteCalendar->findBy(
                    array(
                        'quote' => $quote
            ));
            foreach ($quoteCalendars as $quoteCalendar) {
                $reposQuoteProductAdditional = $this->getDoctrine()->getRepository(QuoteProductAdditional::class);
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
        $basket = $serviceCommon->setBasket();
        return $this->redirectToRoute('index_cart');
    }

    /**
     * @Route("/coupon/", name="add_coupon")
     * @param Request $request
     * @return type ControllerTrait
     */
    public function couponAction(Request $request, ServiceCart $serviceCart, ServiceCommon $serviceCommon, TranslatorInterface $translator) {
        //$request->getLocale()
        $lang = $serviceCommon->getLanguage();
        $coupon = $request->request->get('coupon');
        $coupon = $serviceCart->getCoupon($coupon, $lang);
        if ($coupon) {
            if ($coupon['counter'] >= $coupon['max_uses']) {
                $this->addFlash("no-coupon", $translator->trans('Code is always used'));
            } else {
//                $em = $this->getDoctrine()->getManager();
//                $quote_id = $this->container->get('session')->get('quote_id');
//                $quote = $em->getRepository(\App\Entity\Quote::class)->findOneById($quote_id);
                $amount_cost = $this->container->get('session')->get('price_subtotal') / 100;
                $amount_cost = number_format($amount_cost * $coupon['percent'], 2);
                $this->container->get('session')->set('amount_subtotal_cost', $amount_cost);
                $this->container->get('session')->set('amount_description', $coupon['description']);
            }
        } else {
            $this->addFlash("no-coupon", $translator->trans('Code is not valid'));
        }
        return $this->redirectToRoute('index_cart');
    }

    /**
     *
     * @param type $product
     * @param type $quote_id
     * @param type $dates
     */
    private function getProductIsInBasket($quote, $product, $dates, $serviceCalendar) {
        $serviceCalendar->setCheckCalendar($product->getId());
        // check if first or last day is reserved
        $request_dates = $serviceCalendar->getRequestCalendarDates($dates);
        $current_dates = $serviceCalendar->getCurrentCalendar();
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
