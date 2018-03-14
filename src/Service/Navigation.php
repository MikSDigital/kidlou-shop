<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\Entity\Category;
use App\Entity\Product;

class Navigation {

    /**
     *
     * @var type integer
     */
    private $_max_level = 7;

    /**
     *
     * @var type em
     */
    private $em;

    /**
     *
     * @var type $requestStack
     */
    private $requestStack;

    /**
     *
     * @var type $request
     */
    private $request;

    /**
     *
     * @var type $container
     */
    private $container;

    /**
     *
     * @var type string
     */
    private $current_url;

    /**
     *
     * @var type array
     */
    private $arr_url_keys;

    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Container $container) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->container = $container;
        $this->setCurrentRequest();
        //$this->setMenu();
    }

    /**
     * set current request
     */
    private function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     *
     * @return type string
     */
    public function getCurrentUrl() {
        return $this->current_url;
    }

    /**
     *
     * @param type $url_key1
     * @param type $url_key2
     * @param type $url_key3
     * @param type $url_key4
     * @param type $url_key5
     * @return boolean
     */
    public function getNavigation($typ, $url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "") {
        $arr_level = array();
        if ($typ == 'product') {
            $count_level = 1;
            $arr_level['category'] = $count_level;
        } else if ($typ == 'content') {
            $count_level = 0;
        } else {
            return array();
        }
        $arr_level = $this->getAllUrlKeys($url_key1, $url_key2, $url_key3, $url_key4, $url_key5, $url_key6, $url_key7, $count_level);
        foreach ($arr_level as $url_key => $level) {
            $navi = $this->em
                    ->getRepository(Category::class)
                    ->init($url_key, $level);
            if (!$navi->getCategory()) {
                // check if product
                $product = $this->em
                        ->getRepository(Product::class)
                        ->findOneBy(array('url_key' => $url_key));
                if (!$product) {
                    return false;
                } else {
                    // check if has no parameters after product for calendar
                    $str_url_key = $product->getUrlKey();
                    if ($level < count($arr_level)) {
                        $difflevel = $level;
                        foreach ($arr_level as $url_key => $level) {
                            if ($level > $difflevel) {
                                $str_url_key .= '/' . $url_key;
                            }
                        }
                    }
                    return $str_url_key;
                }
            }
        }
        $arr_navi = array();
        //return $navi;
        if ($navi->getChildren()) {
            foreach ($navi->getChildren() as $child) {
                $arr_navi[] = $child;
            }
        } else {
            $arr_navi[] = $navi;
        }
        return $arr_navi;
    }

    /**
     *
     * @param type $url_key1
     * @param type $url_key2
     * @param type $url_key3
     * @param type $url_key4
     * @param type $url_key5
     * @param type $url_key6
     * @param type $url_key7
     * @param type $count_level
     * @return type
     */
    public function getAllUrlKeys($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "", $count_level) {
        $arr_level = array();
        for ($level = 1; $level <= $this->_max_level; $level++) {
            $variable = ${"url_key" . $level};
            if (!empty($variable)) {
                $count_level++;
                $arr_level[$variable] = $count_level;
            }
        }
        return $arr_level;
    }

    /**
     *
     * @param type $url_key1
     * @param type $url_key2
     * @param type $url_key3
     * @param type $url_key4
     * @param type $url_key5
     * @param type $url_key6
     * @param type $url_key7
     * @return type string
     */
    public function getProductIdFromUrl($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "") {
        for ($level = 1; $level <= $this->_max_level; $level++) {
            $variable = ${"url_key" . $level};
        }
        return $variable;
    }

    /**
     *
     * @param type $url_key
     * @param type $category
     */
    public function setProductPathFromUrl($url_key = '', $category = '') {
        if ($url_key != '') {
            $this->arr_url_keys = array();

            $product = $this->em->getRepository(Product::class)->findOneBy(array('url_key' => $url_key));
            if ($product) {
                foreach ($product->getCategories() as $category) {
                    $this->setProductPathFromUrl('', $category);
                }
            } else {
                $category = $this->em->getRepository(Category::class)->findOneBy(array('url_key' => $url_key));
                $this->setProductPathFromUrl('', $category);
            }
        } else {
            if ($category->getLevel() > 1) {
                $this->arr_url_keys[] = $category->getUrlKey();
                $category = $this->em->getRepository(Category::class)->findOneBy(array('id' => $category->getParentId()));
                $this->setProductPathFromUrl('', $category);
            }
        }
        return $this;
    }

    /**
     *
     * @return type array
     */
    public function getProductPathFromUrl() {
        $arr_urls = array();
        $arr_data = array_reverse($this->arr_url_keys);
        foreach ($arr_data as $key => $url) {
            $arr_urls['url_key' . ($key + 1)] = $url;
        }
        return $arr_urls;
    }

    /**
     *
     * @return type array
     */
    public function showCurrentUrlPath() {

        $arr_urls = explode('/', $this->getCurrentUrl());
        $str_path = '';
        foreach ($arr_urls as $key => $url_key) {
            if ($url_key != '') {
                $temp_arr_urls = array();
                for ($i = 0; $i <= $key; $i++) {
                    $temp_arr_urls[] = $arr_urls[$i];
                }
                if ($url_key != 'category') {
                    $str_path .= ' / <a href="' . implode('/', $temp_arr_urls) . '">' . $url_key . '</a>';
                } else {
                    $str_path .= ' / ' . $url_key;
                }
            }
        }
        return $str_path;
    }

    /**
     *
     * @return type string
     */
    public function showCurrentTitle() {
        $arr_urls = explode('/', $this->getCurrentUrl());
        $temp_arr_urls = array();
        foreach ($arr_urls as $key => $url) {
            if ($url != '') {
                $temp_arr_urls[] = $url;
            }
        }
        $category = $this->em->getRepository(Category::class)->findOneBy(array('url_key' => $temp_arr_urls[(count($temp_arr_urls) - 1)]));
        // check if category exists for this url
        if (!$category) {
            return $temp_arr_urls[(count($temp_arr_urls) - 1)];
        }
        foreach ($category->getLabels() as $label) {
            if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                return $label->getName();
            }
        }
    }

    /**
     *
     * @param type $navi
     * @return type
     */
    protected function _getAllCategories($navi) {
        if ($category->getChildren()) {
            foreach ($category->getChildren() as $child) {
                return $this->_getNextLevel($child);
                //echo $child->getId() . '<br />';
            }
        } else {
            echo $category->getId();
        }
    }

    protected function _getNextLevel($child) {
        $navi = $this->em
                ->getRepository(Category::class)
                ->init($child->getCategory()->getUrlKey(), $child->getCategory()->getLevel());
        return $navi;
    }

    /**
     *
     * @return type string
     */
    public function setMenu() {
        $this->current_url = $this->request->getPathInfo();
        $this->arr_nav_html['product']['title'] = '';
        $this->arr_nav_html['product']['layout'] = '';
        $this->arr_nav_html['product']['responsive'] = '';
        $this->arr_nav_html['cms']['layout'] = '';
        $this->arr_nav_html['cms']['responsive'] = '';
        $this->arr_nav_html['blog']['layout'] = '';
        $this->arr_nav_html['blog']['responsive'] = '';
        $this->setMainMenuProduct();
        $navigations = $this->em->getRepository(Category::class)->getNavigation('PROD', 2);
        $this->setMenuProducts($navigations);
        $navigations = $this->em->getRepository(Category::class)->getNavigation('CMS', 1);
        $this->setMenuCms($navigations);
        $navigations = $this->em->getRepository(Category::class)->getNavigation('BLOG', 1);
        $this->setMenuBlog($navigations);
    }

    /**
     *
     * @param type $navigations
     * @param type $url_key
     */
    private function setMenuProducts($navigations, $url_key = '') {
        // All Kategories
        foreach ($navigations as $navigation) {
            $name = '';
            foreach ($navigation->getLabels() as $label) {
                if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                    $name = $label->getName();
                }
            }
            $url = '/' . $this->request->getLocale() . '/category/' . $url_key . $navigation->getUrlKey() . '/';
            $css_class = '';
            if ($url == $this->getCurrentUrl()) {
                $css_class = 'active';
            }

            $_navigations = $this->em->getRepository(Category::class)->findBy(array('parent_id' => $navigation->getId(), 'status' => 1));
            if (count($_navigations)) {
                $this->arr_nav_html['product']['layout'] .= '<div class="pt_menu hasMenu"'
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<a href="' . $url . '" >' . $name . ' <i class="fa fa-chevron-right"></i></a>'
                        . '<div class="hideMenu submenu submenu-' . ($navigation->getLevel() + 1) . '">';
                $this->arr_nav_html['product']['responsive'] .= '<li class="dropdown-submenu">'
                        . '<a class="' . $css_class . '" href="#" class="dropdown-toggle" '
                        . 'data-toggle="dropdown" role="button" '
                        . 'aria-haspopup="true" '
                        . 'aria-expanded="false">' . $name . ' <span class="caret"></span>'
                        . '</a>'
                        . '<ul class="dropdown-menu">';
                $this->setMenuProducts($_navigations, $navigation->getUrlKey() . '/');
                $this->arr_nav_html['product']['layout'] .= '</div></div>';
                $this->arr_nav_html['product']['responsive'] .= '</ul></li>';
            } else {
                $this->arr_nav_html['product']['layout'] .= '<div class="pt_menu"'
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<a href="' . $url . '" ><span></span>' . $name . '</a>'
                        . '</div>';
                $this->arr_nav_html['product']['responsive'] .= '<li><a class="' . $css_class . '" href="' . $url . '" >' . $name . '</a></li>';
            }
        }
//        return $html;
    }

    /**
     *
     * @param type $navigations
     * @param type $url_key
     * @return string
     */
    private function setMenuCms($navigations, $url_key = '') {
        foreach ($navigations as $navigation) {
            $name = '';
            foreach ($navigation->getLabels() as $label) {
                if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                    $name = $label->getName();
                }
            }
            $url = '/' . $this->request->getLocale() . '/' . $url_key . $navigation->getUrlKey() . '/';
            $css_class = '';
            if ($url == $this->getCurrentUrl()) {
                $css_class = 'active';
            }
            $_navigations = $this->em->getRepository(Category::class)->findBy(array('parent_id' => $navigation->getId(), 'status' => 1));
            if (count($_navigations)) {
                $this->arr_nav_html['cms']['layout'] .= '<li '
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<span class="submenu">'
                        . $name
                        . ' <i class="fa fa-chevron-down"></i></span>'
                        . '<ul class="submenu submenu-' . ($navigation->getLevel() + 1) . '">';
                $this->arr_nav_html['cms']['responsive'] .= '<li class="dropdown-submenu">'
                        . '<a class="' . $css_class . '" href="#" class="dropdown-toggle" '
                        . 'data-toggle="dropdown" role="button" '
                        . 'aria-haspopup="true" '
                        . 'aria-expanded="false">' . $name . ' <span class="caret"></span>'
                        . '</a>'
                        . '<ul class="dropdown-menu">';
                $this->setMenuCms($_navigations, $navigation->getUrlKey() . '/');
                $this->arr_nav_html['cms']['layout'] .= '</ul></li>';
                $this->arr_nav_html['cms']['responsive'] .= '</ul></li>';
            } else {
                $this->arr_nav_html['cms']['layout'] .= '<li '
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<a href="' . $url . '" >' . $name . '</a>'
                        . '</li>';
                $this->arr_nav_html['cms']['responsive'] .= '<li><a class="' . $css_class . '" href="' . $url . '" >' . $name . '</a></li>';
            }
        }
        //return $html;
    }

    /**
     *
     * @param type $navigations
     * @param type $url_key
     * @return string
     */
    private function setMenuBlog($navigations, $url_key = '') {
        foreach ($navigations as $navigation) {
            $name = '';
            foreach ($navigation->getLabels() as $label) {
                if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                    $name = $label->getName();
                }
            }
            $url = '/' . $this->request->getLocale() . '/' . $url_key . $navigation->getUrlKey() . '/';
            $css_class = '';
            if ($url == $this->getCurrentUrl()) {
                $css_class = 'active';
            }
            $_navigations = $this->em->getRepository(Category::class)->findBy(array('parent_id' => $navigation->getId(), 'status' => 1));
            if (count($_navigations)) {
                $this->arr_nav_html['blog']['layout'] .= '<li '
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<span class="submenu">'
                        . '<a class="nav-submenu" href="' . $this->container->get('router')->generate('content', array('url_key1' => $navigation->getUrlKey())) . '">' . $name . '</a>'
                        . ' <i class="fa fa-chevron-down"></i></span>'
                        . '<ul class="submenu submenu-' . ($navigation->getLevel() + 1) . '">';
                $this->arr_nav_html['blog']['responsive'] .= '<li class="dropdown-submenu">'
                        . '<a class="' . $css_class . '" href="' . $this->container->get('router')->generate('content', array('url_key1' => $navigation->getUrlKey())) . '" class="dropdown-toggle" '
                        . 'data-toggle="dropdown" role="button" '
                        . 'aria-haspopup="true" '
                        . 'aria-expanded="false">' . $name . ' <span class="caret"></span>'
                        . '</a>'
                        . '<ul class="dropdown-menu">';
                $this->setMenuBlog($_navigations, $navigation->getUrlKey() . '/');
                $this->arr_nav_html['blog']['layout'] .= '</ul></li>';
                $this->arr_nav_html['blog']['responsive'] .= '</ul></li>';
            } else {
                $this->arr_nav_html['blog']['layout'] .= '<li '
                        . 'data-level="' . $navigation->getLevel() . '"'
                        . 'data-catid="' . $navigation->getId() . '"'
                        . 'data-cattyp="' . $navigation->getTyp()->getId() . '">'
                        . '<a href="' . $url . '" >' . $name . '</a>'
                        . '</li>';
                $this->arr_nav_html['blog']['responsive'] .= '<li><a class="' . $css_class . '" href="' . $url . '" >' . $name . '</a></li>';
            }
        }
//        return $html;
    }

    public function setMainMenuProduct() {
        $navigations = $this->em->getRepository(Category::class)->getNavigation('PROD', 1);
        foreach ($navigations as $navigation) {
            if ($navigation->getLevel() == 1) {
                foreach ($navigation->getLabels() as $label) {
                    if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                        $this->arr_nav_html['product']['title'] = $label->getName();
                    }
                }
            }
        }
        return $this;
    }

    public function getMainMenuProduct1() {
        $navigations = $this->em->getRepository(Category::class)->getNavigation('PROD', 1);
        foreach ($navigations as $navigation) {
            if ($navigation->getLevel() == 1) {
                foreach ($navigation->getLabels() as $label) {
                    if ($label->getLang()->getShortName() == $this->request->getLocale()) {
                        return $label->getName();
                    }
                }
            }
        }
    }

    /**
     *
     * @return string $html
     */
    public function getMainMenuProduct() {
        // Hauptkategorie Kategorie
        return $this->arr_nav_html['product']['title'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuProductsLayout() {
        return $this->arr_nav_html['product']['layout'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuProductsResponsive() {
        return $this->arr_nav_html['product']['responsive'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuCmsLayout() {
        return $this->arr_nav_html['cms']['layout'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuCmsResponsive() {
        return $this->arr_nav_html['cms']['responsive'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuBlogLayout() {
        return $this->arr_nav_html['blog']['layout'];
    }

    /**
     *
     * @return $html
     */
    public function getMenuBlogResponsive() {
        return $this->arr_nav_html['blog']['responsive'];
    }

    /**
     *
     * @param type $url_key1
     * @param type $url_key2
     * @param type $url_key3
     * @param type $url_key4
     * @param type $url_key5
     * @param type $url_key6
     * @param type $url_key7
     * @return type string
     */
    public function getRouteName($url_key1 = "", $url_key2 = "", $url_key3 = "", $url_key4 = "", $url_key5 = "", $url_key6 = "", $url_key7 = "") {
        $arr_level = array();
        for ($level = 1; $level <= $this->_max_level; $level++) {
            $variable = ${"url_key" . $level};
            if (!empty($variable)) {
                $arr_level["url_key" . $level] = $variable;
            }
        }
        return $arr_level;
    }

}
