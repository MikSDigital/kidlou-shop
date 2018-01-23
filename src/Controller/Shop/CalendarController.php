<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Calendar As ServiceCalendar;

/**
 * @Route("/calendar")
 */
class CalendarController extends Controller {

    /**
     * @Route("/", name="calendar_index_index")
     * @Route("/{year}/{month}/{product_id}/", name="calendar_index")
     */
    public function indexAction($year, $month, $product_id, ServiceCalendar $serviceCalendar) {
        $html = $serviceCalendar->getHtmlCalendar($product_id);
        return new JsonResponse($html);
    }

    /**
     * @Route("/countdaysfromto/", name="calendar_count_days_from_to")
     */
    public function countDaysFromToAction(ServiceCalendar $serviceCalendar) {
        $html = $serviceCalendar->getCountDaysFromTo();
        return new JsonResponse($html);
    }

}
