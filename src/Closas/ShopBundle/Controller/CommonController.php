<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\ShopBundle\Entity\Repository\Postalcode;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Helper\Common As HelperCommon;

/**
 * @Route("/common")
 */
class CommonController extends Controller {

    /**
     * @Template("ClosasShopBundle/Common/zone.html.twig")
     * @Route("/zone/", name="zone_area")
     */
    public function zoneAction() {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle/Cart/empty.html.twig');
        }
        return array();
    }

    /**
     * @Route("/geodata/", name="geodata")
     */
    public function geodataAction(Request $request, HelperCommon $helperCommon) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('ClosasShopBundle/Cart/empty.html.twig');
        }
        return $helperCommon->getGeodataPlzCity();
    }

    /**
     * @Route("/geodatastart/", name="geodata_start")
     */
    public function geodataStartAction(HelperCommon $helperCommon) {
        return $helperCommon->getGeodataPlzCity();
    }

    /**
     * @Route("/search/", name="search")
     */
    public function searchAction(HelperCommon $helperCommon) {
        return $helperCommon->getSearchData();
    }

    /**
     * @Route("/paymentcash/{typ}/", name="payment_cash")
     */
    public function paymentCashAction($typ, HelperCommon $helperCommon) {
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
        $total_price = $helperCommon->getShippingCost() + $helperCommon->getCautionCost() + $cash_cost + $this->container->get('session')->get('price_subtotal');
        $txt_total_price = $helperCommon->getCurrencyCode() . ' ' . number_format($total_price, 2);
        if ($cash_cost) {
            $html_cash_cost = $this->renderView('ClosasShopBundle/Common/paymentCash.html.twig');
            return new JsonResponse(array('html_cash_cost' => $html_cash_cost, 'txt_total_price' => $txt_total_price));
        } else {
            return new JsonResponse(array('html_cash_cost' => '', 'txt_total_price' => $txt_total_price));
        }
    }

}
