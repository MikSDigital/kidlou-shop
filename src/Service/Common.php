<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\Service\Navigation;

class Common {

    /**
     * secret key
     * @var string
     */
    private $secret_key = '$7aeUKokU81';

    /**
     * key
     * @var string
     */
    private $key = 'kidlou.com';

    /**
     *
     * @var string
     */
    private $image_key = 'image+Kidlou+100';

    /**
     *
     * @var collection
     */
    private $navcollection;

    /**
     *
     * @var collection
     */
    private $navlabelcollection;

    /**
     *
     * @var array
     */
    private $arr_parent_ids = array();

    /**
     *
     * @var entityManager
     */
    protected $em;

    /**
     *
     * @var requestStack
     */
    private $requestStack;

    /**
     *
     * @var translator
     */
    private $translator;

    /**
     *
     * @var request
     */
    private $request;

    /**
     *
     * @var container $container
     */
    private $container;

    /**
     *
     * @var arr_str
     */
    private $arr_str = array();

    /**
     *
     * @var type arr_cat
     */
    private $arr_cat = array();

    /**
     *
     * @var type string
     */
    private $currency_code = 'CHF';

    /**
     *
     * @var type integer
     */
    private $total_price = 0;

    /**
     *
     * @var type zone
     */
    private $zone;

    /**
     *
     * @var type caution
     */
    private $caution;

    /**
     *
     * @var type array
     */
    private $arr_nav_html = array();

    /**
     *
     * @var type string
     */
    private $html_page_break;

    /**
     *
     * @var type string
     */
    private $html_page_limit;

    /**
     *
     * @var type integer
     */
    private $items_page = 6;

    /**
     *
     * @var type array
     */
    private $arr_page_limit = array(3, 6, 9, 12);

    /**
     *
     * @var type navigation
     */
    private $service_navigation;

    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Translator $translator, Container $container, Navigation $navigation) {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->container = $container;
        $this->service_navigation = $navigation;
        $this->setCurrentRequest();
        $this->setZone();
        $this->setCaution();
        $this->setShippings();
    }

    /**
     * set current request
     */
    protected function setCurrentRequest() {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     *
     * @return request $request
     */
    protected function getRequest() {
        return $this->request;
    }

    /**
     *
     * @return container $container
     */
    protected function getContainer() {
        return $this->container;
    }

    /**
     *
     * @return language $language
     */
    public function getLanguage() {
        return $this->em->getRepository(\App\Entity\Language::class)
                        ->findOneBy(array('short_name' => $this->getRequest()->getLocale()));
    }

    /**
     *
     * @return array
     */
    public function getLanguageAsArray() {
        $langs = $this->em->getRepository(\App\Entity\Language::class)->findAll();
        $arr_lang = array();
        foreach ($langs as $lang) {
            $arr_lang[$lang->getId()] = $lang->getName();
        }
        return $arr_lang;
    }

    /**
     *
     * @return $langid
     */
    public function getLanguageId($shortname) {
        $langs = $this->em->getRepository(\App\Entity\Language::class)->findAll();
        $langid = 0;
        foreach ($langs as $lang) {
            if ($lang->getShortName() == $shortname) {
                $langid = $lang->getId();
                break;
            }
        }
        return $langid;
    }

    /**
     * @param type $string
     * @return string
     */
    public function encrypt($product) {
        $string = $this->secret_key . '__' . $product->getSku() . '__' . time() . '__' . $product->getId();
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    /**
     *
     * @param string $string
     * @param string $key
     * @return string
     */
    public function decrypt($string) {
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }

    /**
     *
     * @param integer $additionalid
     * @param string $key
     * @return string
     */
    public function getAdditional($additionalid) {
        return $this->em
                        ->getRepository(\App\Entity\Product::class)
                        ->findOneById($additionalid);
    }

    /**
     *
     * @param integer $additionalid
     * @param string $key
     * @return string
     */
    public function getAdditionalEncrypt($additionalid) {
        $product = $this->em
                ->getRepository(\App\Entity\Product::class)
                ->findOneById($additionalid);
        return $this->encrypt($product);
    }

    public function getLastDay($beg_datum, $count_days) {
        $lastDay = '';
        $lastDay = new \DateTime($beg_datum);
        return $lastDay->format('d-m-Y');
    }

    /**
     *
     * @param number $price_total
     * @return number
     */
    public function getShippingCost() {
        $arr_shipping_cost = array();
        foreach ($this->getShippings() as $shipping) {
            $arr_shipping_cost[$shipping->getPriceLimit()] = $shipping->getPrice();
        }
        $price_shipping_cost = 0;
        foreach ($arr_shipping_cost as $sale_cost => $shipping_cost) {
            if ($this->container->get('session')->get('price_subtotal') >= $sale_cost) {
                $price_shipping_cost = $shipping_cost;
            } else {
                break;
            }
        }
        return $price_shipping_cost;
    }

    /**
     *
     * @return type cautioncost
     */
    public function getCautionCost() {
        return $this->getCaution()->getPrice();
    }

    /**
     *
     * @param type $percent
     * @return float
     */
    public function getGiftCost($percent) {
        $price = $this->container->get('session')->get('price_subtotal') / 100;
        $price = $price * $percent;
        return number_format($price, 2);
    }

    /**
     * @return number
     */
    public function getStartLatitude() {
        return $this->getZone()->getLatitude();
    }

    /**
     * @return number
     */
    public function getStartLongitude() {
        return $this->getZone()->getLongitude();
    }

    /**
     *
     * @return int
     */
    public function getZoneKmForGoogle() {
        return $this->getZoneKm() * 1000;
    }

    /**
     *
     * @return int
     */
    public function getZoneKm() {
        return $this->getZone()->getDistance();
    }

    /**
     *
     * @return real
     */
    public function getEquatorialRadiusKm() {
        return $this->getZone()->getEquatorialRadius();
    }

    /**
     * set zone
     */
    private function setZone() {
        $zones = $this->em->getRepository(\App\Entity\Zone::class)->findAll();
        foreach ($zones as $zone) {
            $this->zone = $zone;
        }
    }

    /**
     *
     * @return type zone
     */
    private function getZone() {
        return $this->zone;
    }

    /**
     * set caution
     */
    private function setCaution() {
        $cautions = $this->em->getRepository(\App\Entity\Caution::class)->findAll();
        foreach ($cautions as $caution) {
            $this->caution = $caution;
        }
    }

    /**
     *
     * @return caution
     */
    private function getCaution() {
        return $this->caution;
    }

    /**
     * set shipping
     */
    private function setShippings() {
        $this->shippings = $this->em->getRepository(\App\Entity\Shipping::class)->findBy(array(), array('price_limit' => 'ASC'));
    }

    /**
     *
     * @return shipping
     */
    private function getShippings() {
        return $this->shippings;
    }

    /**
     *
     * @param type $string
     * @return string
     */
    public function getDistance($postalcode) {
        $latitudeB = $postalcode->getLatitude();
        $longitudeB = $postalcode->getLongitude();
        $latitudeA = $this->getStartLatitude();
        $longitudeA = $this->getStartLongitude();
        $latA = $latitudeA / 180 * 3.14159265358979;
        $lonA = $longitudeA / 180 * 3.14159265358979;
        $latB = $latitudeB / 180 * 3.14159265358979;
        $lonB = $longitudeB / 180 * 3.14159265358979;
        $distance = acos(sin($latA) * sin($latB) + cos($latA) * cos($latB) * cos($lonB - $lonA)) * $this->getEquatorialRadiusKm();
        return $distance;
    }

    /**
     * Get plz and city
     * @return \App\Service\JsonResponse
     */
    public function getGeodataPlzCity() {
        $plzname = $this->request->get('term');
        $reposPostalcode = $this->em->getRepository(\App\Entity\Postalcode::class);
        $postalcodes = $reposPostalcode->findAllPlzName($plzname);
        $arr_res = array();
        foreach ($postalcodes as $key => $postalcode) {
            $arr_res[$key]["label"] = $postalcode->getPlz() . " " . $postalcode->getName();
            $arr_res[$key]["value"] = $postalcode->getPlz() . "___" . $postalcode->getName() . "___" . round($this->getDistance($postalcode)) . "___" . $this->getZoneKm();
        }
        return new JsonResponse($arr_res);
    }

    /**
     * Get plz and city
     * @return \App\Service\JsonResponse
     */
    public function getSearchData($helperNavigation) {
        $name = $this->getRequest()->get('term');
        $lang = $this->getRequest()->getLocale();
        $products = $this->em->getRepository(\App\Entity\Product::class)->findShopProducts($name, $lang);
        $arr_res = array();
        foreach ($products as $key => $product) {
            $arr_res[$key]["value"] = $product['sku'];
            $arr_res[$key]["name"] = $product['name'];
            $arr_res[$key]["filename"] = '/' . $product['path'] . $product['image'];
            $arr_urls = array();
            $arr_route_name = $helperNavigation->setProductPathFromUrl($product['url_key'])->getProductPathFromUrl();
            foreach ($arr_route_name as $url_name => $name) {
                $arr_urls[$url_name] = $name;
            }
            $url_index = count($arr_urls) + 1;
            $arr_urls['url_key' . $url_index] = $product['url_key'];
            $arr_res[$key]["href"] = $this->getContainer()->get('router')->generate('category_product' . $url_index, $arr_urls);
        }
        return new JsonResponse($arr_res);
    }

    /**
     * @return string
     */
    public function getImageName($sku, $name, $sizename) {
        return md5($sku . $name . $sizename . $this->image_key);
    }

    /**
     * @param type $navigation
     * @param type $is
     * @param type $isFrontend
     * @return type array
     */
    public function getNavigation($navigation = '', $is = true, $isFrontend = false) {
        $this->arr_str = array();
        $this->navcollection = array();
        $this->navlabelcollection = array();
        if (!$isFrontend) {
            $this->navcollection = $this->em
                    ->getRepository(\App\Entity\Category::class)
                    ->findAll();
        } else {
            $this->navcollection = $this->em
                    ->getRepository(\App\Entity\Category::class)
                    ->findBy(array('status' => 1), array('level' => 'ASC', 'order' => 'ASC'));
        }
        $lang = $this->em->getRepository(\App\Entity\Language::class)->findOneBy(array('short_name' => $this->request->getLocale()));
        $this->navlabelcollection = $this->em
                ->getRepository(\App\Entity\Category\Label::class)
                ->findBy(array('lang' => $lang));

        foreach ($this->navcollection as $category) {
// root nicht anzeigen
            if ($navigation != '') {
                if ($is) {
                    if ($category->getId() > 1 && $category->getLevel() == 1 && $category->getUrlKey() == $navigation) {
                        $this->getSub($category, $navigation);
                    }
                } else {
                    if ($category->getId() > 1 && $category->getLevel() == 1 && $category->getUrlKey() != $navigation) {
                        $this->getSub($category, $navigation);
                    }
                }
            } else {
                if ($category->getId() > 1 && $category->getLevel() == 1) {
                    $this->getSub($category, $navigation);
                }
            }
        }

// indexieren
        $i = 0;
        foreach ($this->arr_str as $catid => $data) {
            $this->arr_str[$catid]['index'] = $i++;
        }

        return $this->arr_str;
    }

    /**
     *
     * @param type $category
     * @param type $root
     */
    private function getSub($category, $root = 'root') {

        $hasSubmenu = FALSE;
        $str = '';
        $arr_submenues = array();

        foreach ($this->navcollection as $navcategory) {

            if ($category->getId() == $navcategory->getParentId()) {
// has submenu
                $hasSubmenu = TRUE;
                $this->arr_str[$category->getId()]['level'] = $category->getLevel();
                $this->arr_str[$category->getId()]['url_key'] = $category->getUrlKey();
                $this->arr_str[$category->getId()]['url_path'] = $this->getUrlPath($category, $root);
                $this->arr_str[$category->getId()]['name'] = $this->getNameNavigation($category);
                $this->arr_str[$category->getId()]['catid'] = $category->getId();
                $this->arr_str[$category->getId()]['status'] = $category->getStatus() == true ? 1 : 0;
                $this->arr_str[$category->getId()]['cattyp'] = $category->getTyp() != NULL ? $category->getTyp()->getId() : 0;
                $this->arr_str[$category->getId()]['order'] = $category->getOrder();
                $this->arr_str[$category->getId()]['parent_id'] = $category->getParentId();
                $this->getSub($navcategory, $root);
            }
        }

        if (!$hasSubmenu) {
            $this->arr_str[$category->getId()]['level'] = $category->getLevel();
            $this->arr_str[$category->getId()]['url_key'] = $category->getUrlKey();
            $this->arr_str[$category->getId()]['url_path'] = $this->getUrlPath($category, $root);
            $this->arr_str[$category->getId()]['name'] = $this->getNameNavigation($category);
            $this->arr_str[$category->getId()]['catid'] = $category->getId();
            $this->arr_str[$category->getId()]['status'] = $category->getStatus() == true ? 1 : 0;
            $this->arr_str[$category->getId()]['cattyp'] = $category->getTyp() != NULL ? $category->getTyp()->getId() : 0;
            $this->arr_str[$category->getId()]['order'] = $category->getOrder();
            $this->arr_str[$category->getId()]['parent_id'] = $category->getParentId();
        }
    }

    /**
     *
     * @param type $category
     * @return type array
     */
    public function getUrlPath($category, $root = 'root') {
        return explode('/', $this->createUrlPath($category, $root));
    }

    /**
     * @param type $category
     * @return string
     */
    private function createUrlPath($category, $root = 'root') {
        if ($category && $category->getUrlKey() != $root) {
            $urlStr = $category->getUrlKey();
            $category = $this->em->getRepository(\App\Entity\Category::class)->findOneById($category->getParentId());
            if ($this->createUrlPath($category, $root) != '') {
                $slash = '/';
            } else {
                $slash = '';
            }
            return $this->createUrlPath($category, $root) . $slash . $urlStr;
        } else {
            return '';
        }
    }

    /**
     * Get data for Slider Nivo
     * @return array
     */
    public function getNivoSliderItem() {
        $slideLists = $this->em->getRepository(\App\Entity\Nivoslider::class)->findBy(array('status' => TRUE));
        $arr_slider_ids = array();
        foreach ($slideLists as $slide) {
            $arr_slider_ids[] = $slide->getId();
        }
        $slide = $this->em
                ->getRepository(\App\Entity\Nivoslider\Item::class)
                ->findAllByArray($this->request->getLocale(), $arr_slider_ids);

        $nivoConfig = $this->em
                ->getRepository(\App\Entity\Nivoslider\Configuration::class)
                ->findFirst();

        $array2 = array();
        if (!is_null($nivoConfig)) {
            if ($nivoConfig->getAnimation() == 'animation1') {
                $array2 = $this->sorting_array($slide, 'giam');
            } else {
                $array2 = $this->sorting_array($slide, 'tang');
            }
        }
        return $array2;
    }

    private function sorting_array($array, $mode = 'tang') {
        if ($mode == 'tang') {
            $length = count($array);
            for ($i = 0; $i < $length - 1; $i++) {
                $k = $i;
                for ($j = $i + 1; $j < $length; $j++)
                    if ($array[$j]['order'] < $array[$k]['order'])
                        $k = $j;
                $t = $array[$k];
                $array[$k] = $array[$i];
                $array[$i] = $t;
            }
        }
        if ($mode == 'giam') {
            $length = count($array);
            for ($i = 0; $i < $length - 1; $i++) {
                $k = $i;
                for ($j = $i + 1; $j < $length; $j++)
                    if ($array[$j]['order'] > $array[$k]['order'])
                        $k = $j;
                $t = $array[$k];
                $array[$k] = $array[$i];
                $array[$i] = $t;
            }
        }
        return $array;
    }

    /**
     * Get data for Category Slider
     * @return obj
     */
    public function setCategoryCarousel($catname, $inner = TRUE) {

        $arr_temp_category = array();
        $this->arr_cat = array();
        $arr_main_category = $this->getNavigation($catname);
        $arr_temp_category = $arr_main_category;
        $level = 0;
        foreach ($arr_temp_category as $catid => $category) {
            if ($category['url_key'] == $catname) {
                $level = $category['level'];
                break;
            }
        }
// suchen nach allen level + 1, das sind die hauptkategorien
        if ($inner) {
            $level = $level + 1;
        }
        foreach ($arr_temp_category as $catid => $category) {
            if ($category['level'] != $level) {
                unset($arr_main_category[$catid]);
            }
        }

        $arr_group_cats = array();
        $arr_search_catid = array();
        $arr_category = $arr_temp_category;
        foreach ($arr_main_category as $maincatid => $main_category) {
            $arr_search_catid[$maincatid] = $maincatid;
            foreach ($arr_category as $catid => $category) {
                foreach ($arr_search_catid as $searchcatid => $search_catid) {
                    if ($category['parent_id'] == $searchcatid) {
                        $arr_search_catid[$catid] = $catid;
                        $arr_group_cats[$searchcatid][$catid] = $catid;
                    }
                }
            }
            if (!isset($arr_group_cats[$maincatid])) {
                $arr_group_cats[$maincatid][] = $maincatid;
            }
        }

        foreach ($arr_main_category as $main_catid => $category) {
            $this->arr_cat['maincategories'][$main_catid] = $this->em->getRepository(\App\Entity\Category::class)->findOneById($main_catid);
        }


// create categories
        foreach ($arr_group_cats as $main_catid => $arr_category) {
            foreach ($arr_category as $categoryid) {
                $this->arr_cat['categories'][$main_catid][] = $this->em->getRepository(\App\Entity\Category::class)->findOneById($categoryid);
            }
        }
        return $this;
    }

    public function getCategories() {
        return $this->arr_cat['categories'];
    }

    public function getCategoryById($catid) {
        return $this->arr_cat['categories'][$catid];
    }

    public function getMainCategories() {
        return $this->arr_cat['maincategories'];
    }

    /**
     *
     * @return navigation
     */
    public function getNavigation2($arr_wrap = '') {
        $this->navcollection = $this->em
                ->getRepository(\App\Entity\Category::class)
                ->findAll();

        $this->navlabelcollection = $this->em
                ->getRepository(\App\Entity\Category\Label::class)
                ->findAll();
        $str = '<ul>';
        foreach ($this->navcollection as $category) {
// root nicht anzeigen
            if ($category->getId() > 1 && $category->getLevel() == 1) {
                $str .= $this->getSub2($category);
            }
        }
        $str .= '</ul>';
        return $str;
    }

    private function getSub2($category) {

        $hasSubmenu = FALSE;
        $str = '';
        $arr_submenues = array();
        foreach ($this->navcollection as $navcategory) {

            if ($category->getId() == $navcategory->getParentId()) {
// has submenu
                $hasSubmenu = TRUE;
                $strStart = '<li><a href = "' . $category->getUrlKey() . '">' . $this->getNameNavigation($category) . '</a><ul>';
                $str .= $this->getSub($navcategory);
                $strEnd = '</ul></li>';
            }
        }

        if (!$hasSubmenu) {
            return '<li><a href = "' . $category->getUrlKey() . '">' . $this->getNameNavigation($category) . '</a></li>';
        }

        return $strStart . $str . $strEnd;
    }

    private function getNameNavigation($category) {
        foreach ($this->navlabelcollection as $navlabel) {
            if ($category == $navlabel->getCategory() && $navlabel->getLang()->getShortName() == $this->request->getLocale()) {
                return $navlabel->getName();
            }
        }
    }

    public function getCategoryTyp($optionval = '') {
        $catTyps = $this->em->getRepository(\App\Entity\Category\Typ::class)
                ->findAll();
        $html = '<select class = "select-category-typ form-control">';
        foreach ($catTyps as $key => $catTyp) {
            if ($catTyp->getShortName() != 'PROD') {
                if ($optionval != '') {
                    if ($catTyp->getId() == $optionval) {
                        $selected = 'selected = "selected"';
                    } else {
                        $selected = '';
                    }
// optionval undefined
                    if ($optionval == 'undefined') {
                        $html .= '<option value = "0">' . $this->translator->trans('Bitte auswählen') . '</option>';
                        $optionval = 'defined';
                    }
                    $html .= '<option ' . $selected . ' value = "' . $catTyp->getId() . '">' . $catTyp->getShortName() . '</option>';
                } else {
                    if ($key == 0) {
                        $selected = 'selected = "selected"';
                    } else {
                        $selected = '';
                    }
                    $html .= '<option ' . $selected . ' value = "' . $catTyp->getId() . '">' . $catTyp->getShortName() . '</option>';
                }
            }
        }

        $html .= '</select>';
        return $html;
    }

    /**
     *
     * @return type collection $languages
     */
    public function getLanguages() {
        return $this->em->getRepository(\App\Entity\Language::class)
                        ->findAll();
    }

    /**
     *
     * @return string country
     */
    public function getCountries() {
        return Intl::getRegionBundle()->getCountryNames($this->getRequest()->getLocale());
    }

    /**
     *
     * @return string currency_code
     */
    public function getCurrencyCode() {
        return $this->currency_code;
    }

    /**
     *
     * @param string $shortName
     * @return string $url
     */
    public function getCurrentLanguageUrl($shortName) {
        return str_replace('/' . $this->getRequest()->getLocale() . '/', '/' . $shortName . '/', $this->getRequest()->getUri());
    }

    /**
     *
     * @return array $basket
     */
    private function _getBasket() {
        $quote_id = $this->getContainer()->get('session')->get('quote_id');
        if (!$quote_id) {
            return FALSE;
        }

        $quote = $this->em->getRepository(\App\Entity\Quote::class)->findOneById($quote_id);
        $basketsByQuote = $this->em->getRepository(\App\Entity\Calendar::class)->getBasketsByQuote($quote);
        $arr_basket = array();
        foreach ($basketsByQuote as $basket) {
            //$arr_basket[$basket['parent']][$basket['calendar_id']]['typ_id'] = $basket['typ_id'];
            if ($basket['children']) {
                $arr_basket[$basket['parent']][$basket['calendar_id']]['children'][] = $basket['children'];
            }
        }
        $obj_basket = array();
        foreach ($arr_basket as $product_id => $baskets) {
            foreach ($baskets as $calendar_id => $basket) {
                $obj_basket[$product_id]['product'] = $this->em->getRepository(\App\Entity\Product::class)->findOneById($product_id);
                $obj_basket[$product_id]['calendar'][] = $this->em->getRepository(\App\Entity\Calendar::class)->findOneById($calendar_id);
                if (isset($basket['children'])) {
                    foreach ($basket['children'] as $children) {
                        $obj_basket[$product_id]['children'][$children] = $this->em->getRepository(\App\Entity\Product::class)->findOneById($children);
                    }
                }
            }
        }
        return $obj_basket;
    }

    /**
     *
     * @param product $product
     * @return string $name
     */
    private function _getProductDescription($product) {
        $name = '';
        foreach ($product->getDescriptions() as $description) {
            if (!is_null($description->getLang())) {
                if ($description->getLang()->getShortName() == $this->getLanguage()->getShortName()) {
                    $name = $description->getName();
                    break;
                }
            }
        }
        return $name;
    }

    /**
     *
     * @param type $product
     * @return string $product_url
     */
    private function _getProductUrl($product) {
        return $this->getContainer()->get('router')->generate('product_detail', array('id' => $product->getUrlKey()));
    }

    /**
     *
     * @param type $product
     * @return string $product_url
     */
    private function _getUrl($name, $arr_params = array()) {
        return $this->getContainer()->get('router')->generate($name, $arr_params);
    }

    /**
     *
     * @param type $product
     * @return string $image
     */
    private function _getImage($product, $imgSize) {
        $image = '';
        foreach ($product->getImages() as $image) {
            if ($image->getSize()->getName() == $imgSize && $image->getIsDefault()) {
                return $image;
            }
        }
        return $image;
    }

    /**
     *
     * @param type $calendars
     * @return array $date
     */
    private function _getCalendarData($calendars) {
        $arr_dates = array();
        foreach ($calendars as $calendar) {
            $calendar->getTyp()->getCountDays();
            $date = new \DateTime($calendar->getDateFrom()->format('Y-m-d'));
            $date->modify('+' . ($calendar->getTyp()->getCountDays() - 1) . ' day');
            $arr_dates[] = $calendar->getDateFrom()->format('d-m-Y') . ' - ' . $date->format('d-m-Y');
        }
        return $arr_dates;
    }

    /**
     *
     * @param integer $price
     */
    public function addTotalPrice($price) {
        $this->total_price = $this->total_price + $price;
        return $this;
    }

    /**
     *
     * @param integer $price
     */
    public function setTotalPrice($price) {
        $this->total_price = $price;
        return $this;
    }

    /**
     *
     * @return integer $total_price
     */
    public function getTotalPrice() {
        return $this->total_price;
    }

    /**
     *
     * @param calendar $calendars
     * @param product $product
     * @return number $product_price
     */
    private function _getSubtotalPrice($calendars, $product) {
        $product_price = 0;
        foreach ($calendars as $calendar) {
            foreach ($product->getPrices() as $price) {
                if ($calendar->getTyp()->getId() == $price->getCalendarTyp()->getId()) {
                    $product_price = $product_price + $price->getValue();
                }
            }
        }
        return $product_price;
    }

    /**
     *
     * @param calendar $calendars
     * @param product $product
     * @return number $product_price
     */
    private function _getPrice($calendars, $product) {
        $product_price = 0;
        foreach ($calendars as $calendar) {
            foreach ($product->getPrices() as $price) {
                if ($calendar->getTyp()->getId() == $price->getCalendarTyp()->getId()) {
                    $product_price = $price->getValue();
                }
            }
        }
        return $product_price;
    }

    /**
     *
     * @return integer count
     */
    public function getCountByBasket() {
        $quote_id = $this->getContainer()->get('session')->get('quote_id');
        if (!$quote_id) {
            $quote_id = 0;
        }

        $quote = $this->em->getRepository(\App\Entity\Quote::class)->findOneById($quote_id);
        $objBasket = $this->em->getRepository(\App\Entity\Calendar::class)->getCountByBasket($quote);
        return count($objBasket);
    }

    /**
     *
     * @return number $total_price
     */
    public function getBasketSubtotal() {
        $baskets = $this->_getBasket();
        foreach ($baskets as $product_id => $basket) {
            $this->addTotalPrice($this->_getSubtotalPrice($basket['calendar'], $basket['product']));
            if (isset($basket['children'])) {
                foreach ($basket['children'] as $children) {
                    $this->addTotalPrice($this->_getSubtotalPrice($basket['calendar'], $children));
                }
            }
        }
        return $this->getTotalPrice();
    }

    /**
     *
     * @return boolean|$this
     */
    public function setBasket() {
        $quote_id = $this->getContainer()->get('session')->get('quote_id');
        if (!$quote_id) {
            return FALSE;
        }
        $basket_items = $this->em->getRepository(\App\Entity\Quote::class)->getBasketItems($quote_id, 'image80', 'image80', $this->getRequest()->getLocale());
        $price = 0;
        foreach ($basket_items as $items) {
            foreach ($items['parent_price'] as $item) {
                $price = $price + $item;
            }
            foreach ($items['children'] as $item) {
                foreach ($item['children_price'] as $children_price) {
                    $price = $price + $children_price;
                }
            }
        }
        $this->getContainer()->get('session')->set('price_subtotal', $price);
        $this->getContainer()->get('session')->set('basket_items', $basket_items);
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getAmountSubtotal() {
        $amount_subtotal = $this->getContainer()->get('session')->get('amount_subtotal_cost');
        if (isset($amount_subtotal)) {
            return $amount_subtotal;
        } else {
            return 0;
        }
    }

    /**
     *
     * @return string
     */
    public function getAmountDescription() {
        $amount_subtotal = $this->getContainer()->get('session')->get('amount_subtotal_cost');
        if (isset($amount_subtotal)) {
            return $this->getContainer()->get('session')->get('amount_description');
        }
    }

    /**
     *
     * @return string
     */
    public function getAmountCode() {
        $amount_code = $this->getContainer()->get('session')->get('amount_code');
        if (isset($amount_code)) {
            return $amount_code;
        }
    }

    /**
     *
     * @return float
     */
    public function getPriceSubtotal() {
        return $this->getContainer()->get('session')->get('price_subtotal');
    }

    /**
     *
     * @return float
     */
    public function getPriceTotal() {
        return $this->getShippingCost() + $this->getCautionCost() + ($this->getPriceSubtotal() - $this->getAmountSubtotal());
    }

    /**
     *
     * @param string $html
     */
    public function sendCommonEmailMessage($subject, $text, $to_email) {
        $mail = $this->em->getRepository(\App\Entity\Mail::class)->findOneBy(array('status' => TRUE, 'type' => 'common'));
        $arr_bcc_mails = '';
        if ($mail->getBccEmail() != '') {
            $arr_bcc_mails = explode(', ', $mail->getBccEmail());
        }
        $message = (new \Swift_Message($subject))
                //->setSubject($subject)
                ->setFrom([$mail->getFromEmail() => $mail->getFromName()])
                //->setBcc($arr_bcc_mails)
                ->setTo($to_email)
                ->setBody($text, 'text/plain');

        $this->getMailercommon()->send($message);
    }

    /**
     *
     * @param type $countpages
     */
    public function setHtmlPageBreaker($countpages) {
        if ($countpages > 1) {
            $this->html_page_break = '<div class="sitepage">';
            $page = $this->getRequest()->get('page');
            $limit = $this->getRequest()->get('limit');
            $str_limit = '';
            if ($limit != '') {
                $str_limit = '&limit=' . $limit;
            }
            if ($page == '') {
                $page = 1;
            }
            if ($page > 1) {
                $this->html_page_break .= '<a href="' . $this->service_navigation->getCurrentUrl() . '?page=' . ($page - 1) . $str_limit . '"><i class="fa fa-chevron-left"></i></a>';
            }
            for ($i = 1; $i <= $countpages; $i++) {
                $this->html_page_break .= '<a href="' . $this->service_navigation->getCurrentUrl() . '?page=' . $i . $str_limit . '"> ' . $i . ' </a>';
            }
            if ($page < $countpages) {
                $this->html_page_break .= '<a href="' . $this->service_navigation->getCurrentUrl() . '?page=' . ($page + 1) . $str_limit . '"><i class="fa fa-chevron-right"></i></a>';
            }
            $this->html_page_break .= '</div>';
        }
    }

    public function getItemsPage() {
        return $this->items_page;
    }

    /**
     *
     * @return type string
     */
    public function getHtmlPageBreaker() {
        return $this->html_page_break;
    }

    /**
     *
     * @param type $countpages
     */
    public function setHtmlPageLimit($countpages) {
        if ($countpages > 1) {
            $limit = $this->getRequest()->get('limit');
            if ($limit == '') {
                $limit = $this->getItemsPage();
            }
            $this->html_page_limit = '<div class="pagelimit">' . $this->translator->trans('Items per page') . ' <select class="custom-select pagelimit-select">';
            foreach ($this->arr_page_limit as $_limit) {
                if ($_limit != $limit) {
                    $this->html_page_limit .= '<option value="' . $_limit . '">' . $_limit . '</option>';
                } else {
                    $this->html_page_limit .= '<option value="' . $_limit . '" selected>' . $_limit . '</option>';
                }
            }
            $this->html_page_limit .= '</select></div>';
        }
    }

    /**
     *
     * @return type string
     */
    public function getHtmlPageLimit() {
        return $this->html_page_limit;
    }

}
