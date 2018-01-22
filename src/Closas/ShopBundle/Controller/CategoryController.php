<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Helper\Navigation As HelperNavigation;
use Closas\ShopBundle\Helper\Product As HelperProduct;
use Closas\ShopBundle\Helper\Common As HelperCommon;
use Closas\ShopBundle\Helper\Order As HelperOrder;

/**
 * @Route("/category")
 */
class CategoryController extends Controller {

    /**
     * @Route("/", name="category_product_main")
     */
    public function indexAction() {
        return $this->forward("ClosasShopBundle:Category:view", array('url_key1' => 'category'));
    }

    /**
     * @Template("ClosasShopBundle/Category/view.html.twig")
     * @Route("/{url_key1}/", defaults={"url_key1" = ""}, name="category_product")
     * @Route("/{url_key1}/{url_key2}/", defaults={"url_key2" = ""}, name="category_product2")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/", defaults={"url_key3" = ""}, name="category_product3")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/", defaults={"url_key4" = ""}, name="category_product4")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/", defaults={"url_key5" = ""}, name="category_product5")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/{url_key6}/", defaults={"url_key6" = ""}, name="category_product6")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/{url_key6}/{url_key7}/", defaults={"url_key7" = ""}, name="category_product7")
     *
     */
    public function viewAction($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "", Request $request, HelperNavigation $helperNavigation, HelperProduct $helperProduct, HelperCommon $helperCommon, HelperOrder $helperOrder) {
        $sitepage = 1;
        $typ = 'product';
        $object = $helperNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
        if (!$object) {
            throw $this->createNotFoundException('The category does not exist');
        }
        $arr_route_name = $helperNavigation->getRouteName($url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
        if (!is_array($object)) {
            // product detail
            $arr_route_name = $helperNavigation->getRouteName($url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7);
            return $this->getDetail($object, $arr_route_name, $request, $helperProduct, $helperOrder);
        } else {
            // categories list
            $products = $helperProduct->getProducts($object);
            $countpages = $helperProduct->getCountPages();
            $helperCommon->setHtmlPageBreaker($countpages);
            $helperCommon->setHtmlPageLimit($countpages);
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
    private function getDetail($id, $arr_route_name, $request, $helperProduct, $helperOrder) {
        $product = $helperProduct->getProduct($id);
        $children = $helperProduct->getChildrenByParent($id);

        $arr_additionals_select = array();
        if (!is_null($request->query->get('additionals'))) {
            $arr_additionals_select = explode(',', $request->query->get('additionals'));
        } else {
            // check if quote
            // get quote
            $quote_id = $this->container->get('session')->get('quote_id');
            if ($quote_id) {

                $quote = $this->getDoctrine()->getRepository('ClosasShopBundle:Quote')->findOneBy(array('id' => $quote_id));
                $additionals = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional')->findBy(array(
                    'quote' => $quote,
                    'parent' => $product->getId()
                        )
                );
                foreach ($additionals as $additional) {
                    $arr_additionals_select[] = $additional->getChildren();
                }
            }
        }

        // get order
        $calendarorders = $helperOrder->getOrderData($product);


        return $this->render('ClosasShopBundle/Category/detail.html.twig', array(
                    'id' => $id,
                    'product' => $product,
                    'arr_route_name' => $arr_route_name,
                    'children' => $children,
                    'additionalsselect' => $arr_additionals_select,
                    'calendarorders' => $calendarorders
                        )
        );
    }

}
