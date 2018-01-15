<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\ShopBundle\Entity\Repository\Postalcode;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/common")
 */
class CommonController extends Controller {

    /**
     * @Template()
     * @Route("/zone/", name="zone_area")
     */
    public function zoneAction() {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle:Cart:empty.html.twig');
        }
        return array();
    }

    /**
     * @Route("/geodata/", name="geodata")
     */
    public function geodataAction(Request $request) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle:Cart:empty.html.twig');
        }
        return $this->get('helper.common')->getGeodataPlzCity();
    }

    /**
     * @Route("/geodatastart/", name="geodata_start")
     */
    public function geodataStartAction() {
        return $this->get('helper.common')->getGeodataPlzCity();
    }

    /**
     * @Route("/search/", name="search")
     */
    public function searchAction() {
        return $this->get('helper.common')->getSearchData();
    }

    /**
     * @Route("/paymentcash/{typ}/", name="payment_cash")
     */
    public function paymentCashAction($typ) {
        $quote_id = $this->container->get('session')->get('quote_id');
        $cash_cost = 0;
        if ($quote_id) {
            if ($typ == 'ca') {
                $pcashs = $this->getDoctrine()->getRepository('ClosasShopBundle:Payment\Cash')->findAll();
                if (count($pcashs)) {
                    foreach ($pcashs as $pcash) {
                        if ($pcash->getPrice()) {
                            $cash_cost = $pcash->getPrice();
                        }
                    }
                }
            }
        }
        $this->container->get('session')->set('cash_cost', $cash_cost);
        // total
        $total_price = $this->get('helper.common')->getShippingCost() + $this->get('helper.common')->getCautionCost() + $cash_cost + $this->container->get('session')->get('price_subtotal');
        $txt_total_price = $this->get('helper.common')->getCurrencyCode() . ' ' . number_format($total_price, 2);
        if ($cash_cost) {
            $html_cash_cost = $this->renderView('ClosasShopBundle:Common:paymentCash.html.twig');
            return new JsonResponse(array('html_cash_cost' => $html_cash_cost, 'txt_total_price' => $txt_total_price));
        } else {
            return new JsonResponse(array('html_cash_cost' => '', 'txt_total_price' => $txt_total_price));
        }
    }

}
