<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Navigation As ServiceNavigation;
use App\Service\Product As ServiceProduct;
use App\Service\Common As ServiceCommon;
use App\Service\Order As ServiceOrder;
use App\Entity\Quote;
use App\Entity\Map\QuoteProductAdditional;

/**
 * @Route("/category")
 */
class CategoryController extends Controller {

    /**
     * @Route("/", name="category_product_main")
     */
    public function indexAction() {
        return $this->forward("App\Controller\Shop\CategoryController::view", array('url_key1' => 'category'));
    }

    /**
     * @Template()
     * @Route("/{url_key1}/", defaults={"url_key1" = ""}, name="category_product")
     * @Route("/{url_key1}/{url_key2}/", defaults={"url_key2" = ""}, name="category_product2")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/", defaults={"url_key3" = ""}, name="category_product3")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/", defaults={"url_key4" = ""}, name="category_product4")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/", defaults={"url_key5" = ""}, name="category_product5")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/{url_key6}/", defaults={"url_key6" = ""}, name="category_product6")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/{url_key6}/{url_key7}/", defaults={"url_key7" = ""}, name="category_product7")
     *
     */
    public function viewAction($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "", Request $request, ServiceNavigation $serviceNavigation, ServiceProduct $serviceProduct, ServiceCommon $serviceCommon, ServiceOrder $serviceOrder) {
        $sitepage = 1;
        $typ = 'product';
        $object = $serviceNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
        if (!$object) {
            throw $this->createNotFoundException('The category does not exist');
        }
        $arr_route_name = $serviceNavigation->getRouteName($url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
        if (!is_array($object)) {
            // product detail
            $arr_route_name = $serviceNavigation->getRouteName($url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
            return $this->getDetail($object, $arr_route_name, $request, $serviceProduct, $serviceOrder);
        } else {
            // categories list
            $products = $serviceProduct->getProducts($object);
            $countpages = $serviceProduct->getCountPages();
            $serviceCommon->setHtmlPageBreaker($countpages);
            $serviceCommon->setHtmlPageLimit($countpages);
        }
        return array(
            'products' => $products,
            'arr_route_name' => $arr_route_name
        );
    }

    /**
     *
     * @param type $id
     * @param type $arr_route_name
     * @return type string
     */
    private function getDetail($id, $arr_route_name, $request, ServiceProduct $serviceProduct, ServiceOrder $serviceOrder) {
        $product = $this->getDoctrine()->getRepository(\App\Entity\Product::class)->getDetailProduct($id, $request->getLocale(), '800', '80');
        //$product = $serviceProduct->getProduct($id);
        //$children = $serviceProduct->getChildrenByParent($id);
        $arr_additionals_select = array();
        if (!is_null($request->query->get('additionals'))) {
            $arr_additionals_select = explode(',', $request->query->get('additionals'));
        } else {
            // check if quote
            // get quote
            $quote_id = $this->container->get('session')->get('quote_id');
            if ($quote_id) {
                $additionals = $this->getDoctrine()->getRepository(\App\Entity\Map\QuoteProductAdditional::class)->getChildrens($quote_id, $product->getProductId());
                foreach ($additionals as $additional) {
                    $arr_additionals_select[] = $additional->getChildren();
                }
            }
        }

        // get order
        $calendarorders = $serviceOrder->getOrderData($product);
        return $this->render('shop/category/detail.html.twig', array(
                    'product' => $product,
                    'arr_route_name' => $arr_route_name,
                    'additionalsselect' => $arr_additionals_select,
                    'calendarorders' => $calendarorders
                        )
        );
    }

}
