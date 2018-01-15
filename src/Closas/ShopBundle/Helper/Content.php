<?php

namespace Closas\ShopBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use Closas\ShopBundle\Helper\Common;

class Content {

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var trequest
     */
    private $request;

    /**
     *
     * @var common
     */
    private $common;

    /**
     *
     * @var em EntityManager
     */
    private $em;

    /**
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param Common $common
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Common $common) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->setCurrentRequest();
        $this->common = $common;
    }

    protected function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    protected function getCommon() {
        return $this->common;
    }

    public function getContentByUrlKey($url_key) {

        $lang = $this->em->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $this->request->getLocale()));
        return $this->em
                        ->getRepository('ClosasShopBundle:Content')
                        ->findByGroupUrlKey($url_key, $lang);
    }

    // hier ist immer nur eine kategorie zu den contents und zwar die letzte
    public function getContents($categories) {
        $lang = $this->em->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $this->request->getLocale()));

        foreach ($categories as $category) {
            $cat_id = $category->getId();
        }
        $category = $this->em->getRepository('ClosasShopBundle:Category')->findOneById($cat_id);
        $arr_content = array();
        if ($category->getStatus()) {
            $contents = $this->em
                    ->getRepository('ClosasShopBundle:Content')
                    ->findByCategory($category, $lang);
            if (count($contents) > 0) {
                $arr_content['content'] = $contents;
                $arr_content['cattyp'] = $category->getTyp();
            } else {
                $arr_content = NULL;
            }
            return $arr_content;
        } else {
            return NULL;
        }
    }

    // hier ist immer nur eine kategorie zu den contents und zwar die letzte
    public function getAllContents($categories) {
        $lang = $this->em->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $this->request->getLocale()));

        $arr_cat_id = array();
        foreach ($categories as $category) {
            $arr_cat_id[] = $category->getId();
        }
        $categories = $this->em->getRepository('ClosasShopBundle:Category')->findById($arr_cat_id);
        $arr_content = array();
        foreach ($categories as $category) {
            if ($category->getStatus()) {
                $contents = $this->em
                        ->getRepository('ClosasShopBundle:Content')
                        ->findByCategory($category, $lang);
                if (count($contents) > 0) {
                    $arr_content['content'][] = $contents;
                    $arr_content['cattyp'][] = $category->getTyp();
                } else {
                    $arr_content['content'][] = NULL;
                    $arr_content['cattyp'][] = NULL;
                }
            }
        }
        return $arr_content;
    }

    /**
     *
     * @param type $categories
     * @return $contents
     */
    public function getCarouselContents($categories, $catname) {
        $arr_category = array();
        $lang = $this->em->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $this->request->getLocale()));
        if (!is_array($categories)) {
            $arr_category[] = $categories;
        }

        foreach ($categories as $category) {
            $arr_category[] = $category;
        }
        $arr_content = array();
        $isNull = TRUE;
        foreach ($arr_category as $key => $category) {
            if ($category->getStatus()) {
                $contents = $this->em
                        ->getRepository('ClosasShopBundle:Content')
                        ->findByCategory($category, $lang);
                if (count($contents) > 0) {
                    $isNull = FALSE;
                    foreach ($contents as $content) {
                        $content->setCategory($category);
                        $url = implode('/', $this->getCommon()->getUrlPath($category));
                        if ($category->getTyp()->getShortName() == $catname) {
                            $url .= '/' . $content->getGroup()->getUrlKey();
                        }
                        $content->setCategoryUrlKey($url);
                        $arr_content[] = $content;
                    }
                }
            }
        }
        if ($isNull) {
            return NULL;
        }
        return $arr_content;
    }

    /**
     *
     * @param string $url_key
     */
    public function getBlogViewContent($url_key) {
        $lang = $this->em->getRepository('ClosasShopBundle:Language')->findOneBy(array('short_name' => $this->request->getLocale()));
        $content = $this->em
                ->getRepository('ClosasShopBundle:Content')
                ->findByGroupUrlKey($url_key, $lang);

        return $content;
    }

    public function getUrlFromCategory($category) {
        return explode('/', $this->createUrlFromCategory($category));
    }

    /**
     * @param type $category
     * @return array
     */
    public function createUrlFromCategory($category) {
        if ($category->getUrlKey() != 'root') {
            $urlStr = $category->getUrlKey();
            $category = $this->em->getRepository('ClosasShopBundle:Category')->findOneById($category->getParentId());
            if ($this->createUrlFromCategory($category) != '') {
                $slash = '/';
            } else {
                $slash = '';
            }
            return $this->createUrlFromCategory($category) . $slash . $urlStr;
        } else {
            return '';
        }
    }

}
