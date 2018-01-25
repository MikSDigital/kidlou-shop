<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\Common As ServiceCommon;
use App\Service\Product As ServiceProduct;
use App\Service\Content As ServiceContent;
use App\Service\Navigation As ServiceNavigation;

class StartController extends Controller {

    /**
     * @Template()
     * @Route("/", name="index_page")
     */
    public function indexAction(ServiceCommon $serviceCommon, ServiceProduct $serviceProduct, ServiceContent $serviceContent) {
        $slide = $serviceCommon->getNivoSliderItem();
        $reposNivoslider = $this->getDoctrine()->getRepository(\App\Entity\Nivoslider\Configuration::class);
        $config = $reposNivoslider->findFirst();
        $carousel = $serviceCommon->setCategoryCarousel('category');
        $carouselcatproducts = $carousel->getMainCategories();
        $htmlproducts = $this->getCarouselProductHtml($carousel, $serviceProduct);
        $carouselhtml = $this->renderView('shop/start/catslider/slider.html.twig', array('categories' => $carouselcatproducts, 'products' => $htmlproducts));
        // zweiter Argument ist für inner
        $carousel = $serviceCommon->setCategoryCarousel('blog', FALSE);
        $carouselblogs = $carousel->getMainCategories();
        $htmlblogs = $this->getCarouselBlogHtml($carousel, $serviceContent);
        $carouselbloghtml = $this->renderView('shop/start/blog/slider.html.twig', array('categories' => $carouselblogs, 'blogs' => $htmlblogs));

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
    private function getCarouselProductHtml($carousel, $serviceProduct) {
        $htmlproducts = '';
        $catsliders = $carousel->getCategories();
        foreach ($catsliders as $maincatid => $catslider) {
            $products = $serviceProduct->getProducts($catslider);
            $htmlproducts .= $this->renderView('shop/start/catslider/product.html.twig', array('catid' => $maincatid, 'products' => $products));
        }
        return $htmlproducts;
    }

    /**
     *
     * @param type $blog
     */
    private function getCarouselBlogHtml($carousel, $serviceContent) {
        $htmlcontents = '';
        $catsliders = $carousel->getCategories();
        foreach ($catsliders as $maincatid => $catslider) {
            $contents = $serviceContent->getCarouselContents($catslider, 'BLOG');
            $htmlcontents .= $this->renderView('shop/start/blog/index.html.twig', array('catid' => $maincatid, 'contents' => $contents));
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
    public function contentAction($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "", $url_key8 = "", Request $request, ServiceNavigation $serviceNavigation, ServiceContent $serviceContent) {
        $typ = 'content';
        $content = NULL;
        $contents = NULL;
        $allcontents = NULL;


        if ($url_key1 == 'blog' && $url_key2 == '') {
            $categories = $serviceNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The content does not exist');
            }
            $allcontents = $serviceContent->getAllContents($categories);
        } else if ($url_key1 == 'blog' && $url_key2 != '' && $url_key3 == '') {
            $categories = $serviceNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The content does not exist');
            }
            $contents = $serviceContent->getContents($categories);
        } else if ($url_key1 == 'blog' && $url_key2 != '' && $url_key3 != '') {
            $content = $serviceContent->getContentByUrlKey($url_key3);
        } else {
            $categories = $serviceNavigation->getNavigation($typ, $url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $url_key8);
            if (!$categories) {
                throw $this->createNotFoundException('The content does not exist');
            }
            $contents = $serviceContent->getContents($categories);
        }

        return array(
            'content' => $content,
            'contents' => $contents,
            'allcontents' => $allcontents,
        );
    }

}
