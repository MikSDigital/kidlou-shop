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
use App\Entity\Quote;
use App\Entity\Calendar;
use App\Entity\Map\QuoteProductAdditional;

/**
 * @Route("/test")
 */
class TestController extends Controller {

    /**
     * @Template()
     * @Route("/index/", name="index_test")
     */
    public function indexAction(Request $request) {
        $datas = $this->getDoctrine()->getRepository(Quote::class)->getBasketItems(37, 'image80', 'image80', $request->getLocale());
        print_r($datas);
        exit;
        return array();
    }

}
