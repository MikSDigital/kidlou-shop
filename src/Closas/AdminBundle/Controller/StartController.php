<?php

namespace Closas\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Closas\ShopBundle\Entity\Product\Typ;
use Closas\ShopBundle\Entity\Product\Image\Size As ProductSize;
use Closas\ShopBundle\Entity\Image\Size As CategorySize;
use Closas\ShopBundle\Entity\Language;
use Closas\ShopBundle\Entity\Category;
use Closas\ShopBundle\Entity\Category\Typ As CategoryTyp;
use Closas\ShopBundle\Entity\Category\Label;
use Closas\ShopBundle\Entity\Postalcode;
use Closas\ShopBundle\Entity\Payment;
use Closas\ShopBundle\Entity\Payment\Lang\Label As PaymentLabel;
use Closas\ShopBundle\Entity\Payment\Bank;
use Closas\ShopBundle\Entity\Payment\Paypal;
use Closas\ShopBundle\Entity\Payment\Post;
use Closas\ShopBundle\Entity\Payment\Cash;
use Closas\ShopBundle\Entity\Order\Status;
use Closas\ShopBundle\Entity\Caution;
use Closas\ShopBundle\Entity\Shipping;
use Closas\ShopBundle\Entity\Mail;
use Closas\ShopBundle\Entity\Zone;

/**
 * @Route("/start")
 */
class StartController extends Controller {

    /**
     * @Route("/")
     */
    public function indexAction() {
        set_time_limit(0);
        $typs = array(
            array('simple', 'SIP'),
            array('additional', 'ADD')
        );
        $reposTyp = $this->getDoctrine()->getRepository('ClosasShopBundle:Product\Typ');
        foreach ($typs as $name) {
            $typ = $reposTyp->findOneByName($name[0]);
            if (!$typ) {
                $typ = new Typ();
            }
            $typ->setName($name[0]);
            $typ->setShortName($name[1]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($typ);
            $em->flush();
        }

        $languages = array(
            array('fr', 'Français'),
            array('de', 'Deutsch'),
            array('en', 'English'),
            array('es', 'Español')
        );

        $reposLang = $this->getDoctrine()->getRepository('ClosasShopBundle:Language');
        foreach ($languages as $_language) {
            $language = $reposLang->findOneByName($_language[1]);
            if (!$language) {
                $language = new Language();
            }
            $language->setShortName($_language[0]);
            $language->setName($_language[1]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($language);
            $em->flush();
        }

        $arr_size = array(
            array('image50', 'media/images/50/', '50'),
            array('image80', 'media/images/80/', '80'),
            array('image200', 'media/images/200/', '200'),
            array('image500', 'media/images/500/', '500'),
            array('image800', 'media/images/800/', '800')
        );

        $arr_repos_size['ProductSize'] = $this->getDoctrine()->getRepository('ClosasShopBundle:Product\Image\Size');
        $arr_repos_size['CategorySize'] = $this->getDoctrine()->getRepository('ClosasShopBundle:Image\Size');
        foreach ($arr_repos_size as $funcName => $reposSize) {
            foreach ($arr_size as $sizes) {
                $size = $reposSize->findOneByName($sizes[0]);
                if (!$size) {
                    if ($funcName == 'CategorySize') {
                        $size = new CategorySize();
                    } else {
                        $size = new ProductSize();
                    }
                }
                $size->setName($sizes[0]);
                $size->setPath($sizes[1]);
                $size->setWidth($sizes[2]);
                $size->setHeight($sizes[2]);
                $em = $this->getDoctrine()->getManager();
                $em->persist($size);
                $em->flush();
            }
        }

        $typs = array(
            array('PROD', 'Product'),
            array('CMS', 'Content Management Sytem'),
            array('BLOG', 'Content Blog')
        );


        $em = $this->getDoctrine()->getManager();
        $reposCategoryTyp = $this->getDoctrine()->getRepository('ClosasShopBundle:Category\Typ');
        foreach ($typs as $typ) {
            $categoryTyp = $reposCategoryTyp->findOneBy(
                    array('short_name' => $typ[0], 'name' => $typ[1])
            );
            if (!$categoryTyp) {
                $categoryTyp = new CategoryTyp();
            }
            $categoryTyp->setShortName($typ[0]);
            $categoryTyp->setName($typ[1]);
            $em->persist($categoryTyp);
            $em->flush();
        }




        $em = $this->getDoctrine()->getManager();
        $reposCategory = $this->getDoctrine()->getRepository('ClosasShopBundle:Category');
        $parentcategory = $reposCategory->findOneBy(
                array('url_key' => 'root', 'level' => 0)
        );
        if (!$parentcategory) {
            $parentcategory = new Category();
            $parentcategory->setStatus(true);
            $parentcategory->setUrlKey('root');
            $parentcategory->setLevel(0);
            $parentcategory->setOrder(0);
            $parentcategory->setParentId(0);
            $em->persist($parentcategory);
            $em->flush();
        }



        // array neue category, parent category level parent, level n
        $arr_category = array(
            array('new_key' => 'category', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 10, 'typ' => 1),
            array('new_key' => 'home', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 20, 'typ' => 2),
            array('new_key' => 'pourquoilouer', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 30, 'typ' => 2),
            array('new_key' => 'commentlouer', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 40, 'typ' => 2),
            array('new_key' => 'faq', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 50, 'typ' => 2),
            array('new_key' => 'blog', 'new_level' => 1, 'parent_key' => 'root', 'parent_level' => 0, 'order' => 60, 'typ' => 3),
            array('new_key' => 'balader', 'new_level' => 2, 'parent_key' => 'category', 'parent_level' => 1, 'order' => 10, 'typ' => 1),
            array('new_key' => 'endormir', 'new_level' => 2, 'parent_key' => 'category', 'parent_level' => 1, 'order' => 20, 'typ' => 1),
            array('new_key' => 'regaler', 'new_level' => 2, 'parent_key' => 'category', 'parent_level' => 1, 'order' => 30, 'typ' => 1),
            array('new_key' => 'fairebeau', 'new_level' => 2, 'parent_key' => 'category', 'parent_level' => 1, 'order' => 40, 'typ' => 1),
            array('new_key' => 'eveiller', 'new_level' => 2, 'parent_key' => 'category', 'parent_level' => 1, 'order' => 50, 'typ' => 1),
            array('new_key' => 'description', 'new_level' => 2, 'parent_key' => 'pourquoilouer', 'parent_level' => 1, 'order' => 10, 'typ' => 2),
            array('new_key' => 'video', 'new_level' => 2, 'parent_key' => 'pourquoilouer', 'parent_level' => 1, 'order' => 20, 'typ' => 2),
            array('new_key' => 'combinee', 'new_level' => 3, 'parent_key' => 'balader', 'parent_level' => 2, 'order' => 10, 'typ' => 1),
            array('new_key' => 'nacelle', 'new_level' => 3, 'parent_key' => 'balader', 'parent_level' => 2, 'order' => 20, 'typ' => 1),
            array('new_key' => 'double', 'new_level' => 3, 'parent_key' => 'balader', 'parent_level' => 2, 'order' => 30, 'typ' => 1),
            array('new_key' => '3roues', 'new_level' => 3, 'parent_key' => 'balader', 'parent_level' => 2, 'order' => 40, 'typ' => 1),
            array('new_key' => '4roues', 'new_level' => 3, 'parent_key' => 'balader', 'parent_level' => 2, 'order' => 50, 'typ' => 1),
        );


        $arr_label = array(
            'category' => array('fr' => 'Category', 'de' => 'Kategorie', 'en' => 'Category', 'es' => 'Categoria'),
            'home' => array('fr' => 'Home', 'de' => 'Home', 'en' => 'Home', 'es' => 'Home'),
            'pourquoilouer' => array('fr' => 'Pourquoi louer?', 'de' => 'Warum mieten?', 'en' => 'Why rent?', 'es' => 'Porque alguilar?'),
            'commentlouer' => array('fr' => 'Comment louer?', 'de' => 'Wie mieten?', 'en' => 'How to rent?', 'es' => 'Como alguilar?'),
            'blog' => array('fr' => 'Blog', 'de' => 'Blog', 'en' => 'Blog', 'es' => 'Blog'),
            'faq' => array('fr' => 'Faq', 'de' => 'Faq', 'en' => 'Faq', 'es' => 'Faq'),
            'balader' => array('fr' => 'Se balader', 'de' => 'Spazieren', 'en' => 'Going on walk', 'es' => 'Pasear'),
            'endormir' => array('fr' => "S'endormir", 'de' => 'Schlafen', 'en' => 'Sleeping', 'es' => 'Dormir tranquilo'),
            'regaler' => array('fr' => 'Se régaler', 'de' => 'Essen', 'en' => 'Eating', 'es' => 'Divertirse'),
            'fairebeau' => array('fr' => 'Se faire beau', 'de' => 'Baden', 'en' => 'Bathing', 'es' => 'Arreglarse'),
            'eveiller' => array('fr' => "S'éveiller", 'de' => 'Spielen', 'en' => 'Playing', 'es' => 'Disfrutar'),
            'description' => array('fr' => "Description", 'de' => 'Übersicht', 'en' => 'Overview', 'es' => 'Descripción'),
            'video' => array('fr' => "Video", 'de' => 'Video', 'en' => 'Video', 'es' => 'Video'),
            'combinee' => array('fr' => "Poussette combinée", 'de' => 'Kombikinderwagen', 'en' => 'pram stroller', 'es' => 'Cochecitos combi para niños'),
            'nacelle' => array('fr' => "Poussette nacelle", 'de' => 'Kinderwagen & Aufsatz', 'en' => 'carrycot & stroller', 'es' => 'Poussette nacelle'),
            'double' => array('fr' => "Poussette double", 'de' => 'Geschwisterwagen', 'en' => 'twin stroller', 'es' => 'Cochecitos de hermanos'),
            '3roues' => array('fr' => "Poussette 3 roues", 'de' => 'Dreiradwagen', 'en' => '3-wheelers', 'es' => 'Cochecito de 3 ruedas'),
            '4roues' => array('fr' => "Poussette 4 roues", 'de' => 'Vierradwagen', 'en' => '4-wheelers', 'es' => 'Cochecito de 4 ruedas')
        );
        $em = $this->getDoctrine()->getManager();
        $reposCatTyp = $this->getDoctrine()->getRepository('ClosasShopBundle:Category\Typ');
        foreach ($arr_category as $data) {
            $parentcategory = $reposCategory->findOneBy(
                    array('url_key' => $data['parent_key'], 'level' => $data['parent_level'])
            );
            $catTyp = $reposCatTyp->findOneById($data['typ']);
            if (!$parentcategory) {
                $parentcategory = new Category();
                $parentcategory->setStatus(true);
                $parentcategory->setUrlKey($data['parent_key']);
                $parentcategory->setLevel($data['parent_level']);
                $parentcategory->setOrder($data['order']);
                $parentcategory->setParentId($parentcategory->getId());
                $parentcategory->setTyp($catTyp);
                $em->persist($parentcategory);
                $em->flush();

                if ($parentcategory->getUrlKey() != 'root') {
                    $reposLanguage = $this->getDoctrine()->getRepository('ClosasShopBundle:Language');
                    $languages = $reposLanguage->findAll();

                    $reposLabel = $this->getDoctrine()->getRepository('ClosasShopBundle:Category\Label');
                    foreach ($languages as $lang) {
                        $label = $reposLabel->findOneBy(
                                array('category' => $parentcategory, 'lang' => $lang)
                        );
                        if (!$label) {
                            $label = new Label();
                        }
                        $label->setName($arr_label[$parentcategory->getUrlKey()][$lang->getShortName()]);
                        $label->setCategory($parentcategory);
                        $label->setLang($lang);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($label);
                        $em->flush();
                    }
                }
            }

            $category = $reposCategory->findOneBy(
                    array('url_key' => $data['new_key'], 'level' => $data['new_level'])
            );
            if (!$category) {
                $category = new Category();
            }

            $category->setStatus(true);
            $category->setUrlKey($data['new_key']);
            $category->setLevel($data['new_level']);
            $category->setOrder($data['order']);
            $category->setTyp($catTyp);
            $category->setParentId($parentcategory->getId());
            $em->persist($category);
            $em->flush();

            $reposLanguage = $this->getDoctrine()->getRepository('ClosasShopBundle:Language');
            $languages = $reposLanguage->findAll();
            $reposLabel = $this->getDoctrine()->getRepository('ClosasShopBundle:Category\Label');

            foreach ($languages as $lang) {
                $label = $reposLabel->findOneBy(
                        array('category' => $category, 'lang' => $lang)
                );
                if (!$label) {
                    $label = new Label();
                }
                $label->setName($arr_label[$category->getUrlKey()][$lang->getShortName()]);
                $label->setCategory($category);
                $label->setLang($lang);
                $em = $this->getDoctrine()->getManager();
                $em->persist($label);
                $em->flush();
            }
        }


        $payments = array(
            'ba' => 'Bank',
            'ca' => 'Cash',
            'pp' => 'Paypal',
            'po' => 'Post'
        );

        $em = $this->getDoctrine()->getManager();
        $reposPayment = $this->getDoctrine()->getRepository('ClosasShopBundle:Payment');
        foreach ($payments as $short_name => $name) {
            $payment = $reposPayment->findOneBy(array('short_name' => $short_name));
            if (!$payment) {
                $payment = new Payment();
            }
            $payment->setShortName($short_name);
            $payment->setName($name);
            $em->persist($payment);
            $em->flush();
            $obj = $this->getDoctrine()->getRepository('ClosasShopBundle:Payment' . '\\' . $name)->findOneBy(array('payment' => $payment));
            if (!$obj) {
                $obj = "Closas\\ShopBundle\\Entity\\Payment\\" . $name;
                $obj = new $obj();
            }
            $obj->setStatus(false);
            $obj->setPayment($payment);
            $em->persist($obj);
            $em->flush();

            $langs = $this->getDoctrine()->getRepository('ClosasShopBundle:Language')->findAll();
            foreach ($langs as $lang) {
                $label = $this->getDoctrine()->getRepository('ClosasShopBundle:Payment\Lang\Label')->findOneBy(array(strtolower($name) => $obj, 'lang' => $lang));
                if (!$label) {
                    $label = new PaymentLabel();
                }
                $label->setTitle($name);
                $label->setLang($lang);
                $objName = 'set' . $name;
                $label->$objName($obj);
                $em->persist($label);
                $em->flush();
            }
        }

        $statuses = array(
            'pending',
            'processing',
            'canceled',
            'holded',
            'complete',
            'closed'
        );
        $reposOrderStatus = $this->getDoctrine()->getRepository('ClosasShopBundle:Order\Status');

        foreach ($statuses as $status) {
            $orderStatus = $reposOrderStatus->findOneBy(array('name' => $status));
            if (!$orderStatus) {
                $orderStatus = new Status();
            }
            $orderStatus->setName($status);
            $em->persist($orderStatus);
            $em->flush();
        }

        // caution
        $caution_price = 100;
        $caution = $this->getDoctrine()->getRepository('ClosasShopBundle:Caution')->findOneBy(array('price' => $caution_price));
        if (!$caution) {
            $caution = new Caution();
        }
        $caution->setPrice($caution_price);
        $em->persist($caution);
        $em->flush();

        // shipping
        $arr_shipping = array(
            '0' => '35',
            '50' => '25',
            '100' => '15',
            '200' => '10',
        );

        foreach ($arr_shipping as $price_limit => $price) {
            $shipping = $this->getDoctrine()->getRepository('ClosasShopBundle:Shipping')->findOneBy(array('price_limit' => $price_limit, 'price' => $price));
            if (!$shipping) {
                $shipping = new Shipping();
            }
            $shipping->setPriceLimit($price_limit);
            $shipping->setPrice($price);
            $em->persist($shipping);
            $em->flush();
        }

        // mail
        $arr_mail = array();
        $arr_mail['product']['from_email'] = 'info@kidlou.com';
        $arr_mail['product']['from_name'] = 'kidlou - location';
        $arr_mail['product']['bcc_email'] = 'miguel@closas.ch';
        $arr_mail['product']['status'] = 1;
        $arr_mail['common']['from_email'] = 'info@kidlou.com';
        $arr_mail['common']['from_name'] = 'kidlou - location';
        $arr_mail['common']['bcc_email'] = 'miguel@closas.ch';
        $arr_mail['common']['status'] = 1;

        foreach ($arr_mail as $type => $value) {
            $mail = $this->getDoctrine()->getRepository('ClosasShopBundle:Mail')->findOneBy(array('type' => $type));
            if (!$mail) {
                $mail = new Mail();
            }
            $mail->setType($type);
            $mail->setFromEmail($arr_mail[$type]['from_email']);
            $mail->setFromName($arr_mail[$type]['from_name']);
            $mail->setBccEmail($arr_mail[$type]['bcc_email']);
            $mail->setStatus($arr_mail[$type]['status']);
            $em->persist($mail);
            $em->flush();
        }

        // zone
        $arr_zone = array();
        $arr_zone['latitude'] = '46.2295000';
        $arr_zone['longitude'] = '7.3568000';
        $arr_zone['equatorial_radius'] = '6378.137';
        $arr_zone['distance'] = '20';
        $zone = $this->getDoctrine()->getRepository('ClosasShopBundle:Zone')->findOneBy(array('latitude' => $arr_zone['latitude']));
        if (!$zone) {
            $zone = new Zone();
        }
        $zone->setLatitude($arr_zone['latitude']);
        $zone->setLongitude($arr_zone['longitude']);
        $zone->setEquatorialRadius($arr_zone['equatorial_radius']);
        $zone->setDistance($arr_zone['distance']);
        $em->persist($zone);
        $em->flush();

        return new Response('Saved');
    }

}
