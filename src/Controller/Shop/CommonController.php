<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Repository\Postalcode;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Common As ServiceCommon;
use App\Service\Navigation As ServiceNavigation;
use App\Service\Payment As ServicePayment;

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
            return $this->render('shop/cart/empty.html.twig');
        }
        return array();
    }

    /**
     * @Route("/geodata/", name="geodata")
     */
    public function geodataAction(Request $request, ServiceCommon $serviceCommon) {
        $quote_id = $this->container->get('session')->get('quote_id');
        if (!$quote_id) {
            return $this->render('shop/cart/empty.html.twig');
        }
        return $serviceCommon->getGeodataPlzCity();
    }

    /**
     * @Route("/geodatastart/", name="geodata_start")
     */
    public function geodataStartAction(ServiceCommon $serviceCommon) {
        return $serviceCommon->getGeodataPlzCity();
    }

    /**
     * @Route("/search/", name="search")
     */
    public function searchAction(ServiceCommon $serviceCommon, ServiceNavigation $serviceNavigation) {
        return $serviceCommon->getSearchData($serviceNavigation);
    }

    /**
     * @Route("/paymenttyp/{typ}/", name="payment_typ")
     */
    public function paymentTypAction($typ, ServiceCommon $serviceCommon, ServicePayment $servicePayment) {
        $quote_id = $this->container->get('session')->get('quote_id');
        $cash_cost = 0;
        if ($quote_id) {
            if ($typ == 'ca') {
                $pcashs = $this->getDoctrine()->getRepository(\App\Entity\Payment\Cash::class)->findAll();
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
        $total_price = $serviceCommon->getShippingCost() + $serviceCommon->getCautionCost() + $cash_cost + $this->container->get('session')->get('price_subtotal');
        $txt_total_price = $serviceCommon->getCurrencyCode() . ' ' . number_format($total_price, 2);
        if ($cash_cost) {
            $html_cash_cost = $this->renderView('shop/common/paymentCash.html.twig');
            return new JsonResponse(array('html_cash_cost' => $html_cash_cost, 'txt_total_price' => $txt_total_price));
        } else {
            return new JsonResponse(array('html_cash_cost' => '', 'txt_total_price' => $txt_total_price));
        }
    }

}
