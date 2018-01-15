<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Helper\Navigation As HelperNavigation;

/**
 * @Route("/product")
 */
class ProductController extends Controller {

    /**
     * @Template()
     * @Route("/view/{url_key1}/", defaults={"url_key1" = ""}, name="product_view")
     * @Route("/view/{url_key1}/{url_key2}/", defaults={"url_key2" = ""})
     * @Route("/view/{url_key1}/{url_key2}/{url_key3}/", defaults={"url_key3" = ""})
     * @Route("/view/{url_key1}/{url_key2}/{url_key3}/{url_key4}/", defaults={"url_key4" = ""})
     * @Route("/view/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/", defaults={"url_key5" = ""})
     */
    public function viewAction($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", HelperNavigation $helperNavigation) {
        $typ = 'product';
        $categories = $helperNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5);
        //$categories = $this->get('helper.navigation')->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5);
        if (!$categories) {
            throw $this->createNotFoundException('The category does not exist');
        }
        $products = $this->get('helper.product')->getProducts($categories);
        return array(
            'products' => $products
        );
    }

    /**
     * @Template()
     * @Route("/detail/{id}", name="product_detail")
     * @Route("/detail/{id}/")
     * @Route("/detail/{id}/{year}/", defaults={"year" = ""})
     * @Route("/detail/{id}/{year}/{month}/", defaults={"month" = ""})
     */
    public function detailAction($id, $year = "", $month = "") {
        $product = $this->get('helper.product')->getProduct($id);
        $children = $this->get('helper.product')->getChildrenByParent($id);
        $arr_quotes_dates = array();

        $calendarQuotes = new ArrayCollection();
        $quoteProductAdditionals = new ArrayCollection();
        // get quote
        $quote_id = $this->container->get('session')->get('quote_id');
        if ($quote_id) {
            $reposQuote = $this->getDoctrine()->getRepository('ClosasShopBundle:Quote');
            $quote = $reposQuote->findOneBy(array('id' => $quote_id));
            $reposCalendar = $this->getDoctrine()->getRepository('ClosasShopBundle:Calendar');
            $calendarQuotes = $reposCalendar->findBy(
                    array(
                        'quote' => $quote,
                        'product' => $product
                    )
            );

            $reposQuoteProductAdditional = $this->getDoctrine()->getRepository('ClosasShopBundle:Map\QuoteProductAdditional');
            $quoteProductAdditionals = $reposQuoteProductAdditional->findBy(
                    array(
                        'quote' => $quote,
                        'parent' => $product->getId()
                    )
            );
        }

        // get order
        $calendarorders = $this->get('helper.order')->getOrderData($product);

        $id = $id;

        return array(
            'id' => $id,
            'product' => $product,
            'children' => $children,
            'calendarorders' => $calendarorders,
            'calendarquotes' => $calendarQuotes,
            'quoteproductadditionals' => $quoteProductAdditionals
        );
    }

}
