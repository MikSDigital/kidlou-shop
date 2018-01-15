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

/**
 * @Route("/test")
 */
class TestController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="index_test")
     */
    public function indexAction(Request $request) {
        $datas = $this->getDoctrine()->getRepository('ClosasShopBundle:Quote')->getBasketItems(37, 'image80', 'image80', $request->getLocale());
        print_r($datas);
        exit;
        return array();
    }

}
