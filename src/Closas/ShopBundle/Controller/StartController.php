<?php

namespace Closas\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Closas\ShopBundle\Helper\Common As HelperCommon;
use Closas\ShopBundle\Helper\Product As HelperProduct;
use Closas\ShopBundle\Helper\Content As HelperContent;
use Closas\ShopBundle\Helper\Navigation As HelperNavigation;

class StartController extends Controller {

    /**
     * @Template("ClosasShopBundle/Start/index.html.twig")
     * @Route("/", name="index_page")
     */
    public function indexAction(HelperCommon $helperCommon, HelperProduct $helperProduct, HelperContent $helperContent) {
        $slide = $helperCommon->getNivoSliderItem();
        $reposNivoslider = $this->getDoctrine()->getRepository('ClosasShopBundle:Nivoslider\Configuration');
        $config = $reposNivoslider->findFirst();
        $carousel = $helperCommon->setCategoryCarousel('category');
        $carouselcatproducts = $carousel->getMainCategories();
        $htmlproducts = $this->getCarouselProductHtml($carousel, $helperProduct);
        $carouselhtml = $this->renderView('ClosasShopBundle/Start/Catslider/slider.html.twig', array('categories' => $carouselcatproducts, 'products' => $htmlproducts));
        // zweiter Argument ist für inner
        $carousel = $helperCommon->setCategoryCarousel('blog', FALSE);
        $carouselblogs = $carousel->getMainCategories();
        $htmlblogs = $this->getCarouselBlogHtml($carousel, $helperContent);
        $carouselbloghtml = $this->renderView('ClosasShopBundle/Start/Blog/slider.html.twig', array('categories' => $carouselblogs, 'blogs' => $htmlblogs));

        return array(
            'config' => $config,
            'slide' => $slide,
            'carouselhtml' => $carouselhtml,
            'carouselcatproducts' => $carouselcatproducts,
            'carouselblogs' => $carouselblogs,
            'carouselbloghtml' => $carouselbloghtml
        );
    }

    /**
     *
     * @param type $carousel
     * @return type string
     */
    private function getCarouselProductHtml($carousel, $helperProduct) {
        $htmlproducts = '';
        $catsliders = $carousel->getCategories();
        foreach ($catsliders as $maincatid => $catslider) {
            $products = $helperProduct->getProducts($catslider);
            $htmlproducts .= $this->renderView('ClosasShopBundle/Start/Catslider/product.html.twig', array('catid' => $maincatid, 'products' => $products));
        }
        return $htmlproducts;
    }

    /**
     *
     * @param type $blog
     */
    private function getCarouselBlogHtml($carousel, $helperContent) {
        $htmlcontents = '';
        $catsliders = $carousel->getCategories();
        foreach ($catsliders as $maincatid => $catslider) {
            $contents = $helperContent->getCarouselContents($catslider, 'BLOG');
            $htmlcontents .= $this->renderView('ClosasShopBundle/Start/Blog/index.html.twig', array('catid' => $maincatid, 'contents' => $contents));
        }
        return $htmlcontents;
    }

    /**
     * @Template()
     * @Route("/{url_key1}/", defaults={"url_key1" = ""}, name="content")
     * @Route("/{url_key1}/{url_key2}/", defaults={"url_key2" = ""}, name="content2")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/", defaults={"url_key3" = ""}, name="content3")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/", defaults={"url_key4" = ""}, name="content4")
     * @Route("/{url_key1}/{url_key2}/{url_key3}/{url_key4}/{url_key5}/", defaults={"url_key5" = ""}, name="content5")
     */
    public function contentAction($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "", $url_key8 = "", Request $request, HelperNavigation $helperNavigation, HelperContent $helperContent) {
        $typ = 'content';
        $content = NULL;
        $contents = NULL;
        $allcontents = NULL;


        if ($url_key1 == 'blog' && $url_key2 == '') {
            $categories = $helperNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The category does not exist');
            }
            $allcontents = $helperContent->getAllContents($categories);
        } else if ($url_key1 == 'blog' && $url_key2 != '' && $url_key3 == '') {
            $categories = $helperNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The category does not exist');
            }
            $contents = $helperContent->getContents($categories);
        } else if ($url_key1 == 'blog' && $url_key2 != '' && $url_key3 != '') {
            $content = $helperContent->getContentByUrlKey($url_key3);
        } else {
            $categories = $helperNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The category does not exist');
            }
            $contents = $helperContent->getContents($categories);
        }

        return array(
            'content' => $content,
            'contents' => $contents,
            'allcontents' => $allcontents,
        );
    }

}
