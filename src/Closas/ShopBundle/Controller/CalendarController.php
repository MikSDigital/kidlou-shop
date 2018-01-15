<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closas\ShopBundle\Helper\Calendar As HelperCalendar;

/**
 * @Route("/calendar")
 */
class CalendarController extends Controller {

    /**
     * @Route("/", name="calendar_index_index")
     * @Route("/{year}/{month}/{product_id}/", name="calendar_index")
     */
    public function indexAction($year, $month, $product_id, HelperCalendar $helperCalendar) {
        $html = $helperCalendar->getHtmlCalendar($product_id);
        return new JsonResponse($html);
    }

    /**
     * @Route("/countdaysfromto/", name="calendar_count_days_from_to")
     */
    public function countDaysFromToAction(HelperCalendar $helperCalendar) {
        $html = $helperCalendar->getCountDaysFromTo();
        return new JsonResponse($html);
    }

}
