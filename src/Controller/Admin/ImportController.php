<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ImageResizer;
use App\Entity\Product;
use App\Entity\Product\Image;
use App\Entity\Product\Image\Size;
use App\Entity\Product\Description;
use App\Entity\Price;
use App\Entity\Map\ProductAdditional;
use App\Entity\Upload;
use App\Service\Common As ServiceCommon;

class ImportController extends Controller {

    protected $_imageSize = array('small' => '80', 'medium' => '200', 'big' => '600');
    protected $_importCsv = 'KIDLOU_PRODUCTS.csv';
    protected $_arr_designation;
    protected $_csv_data;
    protected $_arr_language;
    protected $_locale;
    protected $_product;
    protected $_arr_originalFile = array();
    protected $_arr_targetFile;
    protected $_arr_deleteFile;

    /**
     * @Template()
     * @Route("/import/{id}/{file}/", defaults={"id" = NULL, "file" = FALSE},  name="admin_import")
     */
    public function importAction($id = NULL, $file = FALSE, ServiceCommon $serviceCommon) {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '128 M');
        if ($id != NULL) {
            $upload = $this->getDoctrine()->getRepository(Upload::class)->findOneById($id);
            $this->_importCsv = $upload->getProducts();
        }
        $this->setDesignation();

        $_importFile = $this->get('kernel')->getRootDir() . '/../public/media/import/csv/' . $this->_importCsv;
        if (($handle = fopen($_importFile, "r")) !== FALSE) {
            //$mem_usage = memory_get_usage(true);
            // delete all files in images
            $this->prepareProduct();
            if ($file) {
                $this->deleteAllImages();
            }
            $this->setTitlesDescription();
            $zeile = 1;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($zeile > 11) {
                    if ($data[0] == '') {
                        continue;
                    }
                    $this->setCsvData($data);
                    // set language code
                    $this->setLanguageLocale();

                    if ($this->_getTyp() != 'SIP' && $this->_getTyp() != 'ADD') {
                        continue;
                    }

                    if (!$this->isSkuAllowed()) {
                        continue;
                    }

                    $this->setProduct();
                    $this->setPrice();
                    $this->setProductDescription();
                    if ($this->getLanguageLocale() == 'fr') {
                        if ($file) {
                            $this->setImages($serviceCommon);
                        }
                    }
                }
                $zeile++;
            }

            if ($file) {
                $this->deleteImagesNotAllowed();
            }

            if (($handle = fopen($_importFile, "r")) !== FALSE) {
                $zeile = 1;
                $reposProduct = $this->getDoctrine()->getRepository(Product::class);
                $reposProductAdditional = $this->getDoctrine()->getRepository(Map\ProductAdditional::class);
                $em = $this->getDoctrine()->getManager();
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($zeile > 11) {
                        $this->setCsvData($data);
                        // set language code
                        $this->setLanguageLocale();
                        if ($this->getLanguageLocale() == 'fr' && $this->_getTyp() == 'SIP') {
                            foreach ($this->_getSkuFromAddtionals() as $add_sku) {
                                $parent = $reposProduct->findOneBySku($this->_getSku());
                                $child = $reposProduct->findOneBySku($add_sku);
                                if (!$parent || !$child) {
                                    continue;
                                }
                                $productAdditional = $reposProductAdditional->findOneBy(
                                        array('parent' => $parent->getId(), 'children' => $child->getId())
                                );
                                if (!$productAdditional) {
                                    $productAdditional = new ProductAdditional();
                                }
                                $productAdditional->setParent($parent->getId());
                                $productAdditional->setChildren($child->getId());
                                $em->persist($productAdditional);
                                $em->flush();
                            }
                        }
                    }
                    $zeile++;
                }
            }
            fclose($handle);

            //gc_collect_cycles();
            return array();
        }
    }

    protected function prepareProduct() {
        $em = $this->getDoctrine()->getManager();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        foreach ($products as $product) {
            $product->setUrlKey(NULL);
            $em->persist($product);
            $em->flush();
        }
    }

    protected function deleteAllImages() {
        $images = $this->getDoctrine()->getRepository(Product\Image::class)->findAll();

        foreach ($images as $image) {
            $filename = $this->get('kernel')->getRootDir() . '/../public/' . $image->getSize()->getPath() . $image->getName();
            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }

    protected function isSkuAllowed() {
        $reposTyp = $this->getDoctrine()->getRepository(Product\Typ::class);
        $typ = $reposTyp->findOneBy(array('short_name' => $this->_getTyp()));
        if (!$typ) {
            return false;
        }
        return true;
    }

    protected function setProduct() {
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $this->_product = $reposProduct->findOneBySku($this->_getSku());
        if (!$this->_product) {
            $this->_product = new Product();
        }
        $em = $this->getDoctrine()->getManager();
        $this->_product->setSku($this->_getSku());
        $this->_product->setStatus(true);

        $reposTyp = $this->getDoctrine()->getRepository(Product\Typ::class);
        $typ = $reposTyp->findOneBy(array('short_name' => $this->_getTyp()));
        $this->_product->setTyp($typ);
        $this->_product->setSale($this->_getPrice() != '' ? $this->_getPrice() : '0');
        // set url_key
        if ($this->getLanguageLocale() == 'fr') {
            $this->_product->setUrlKey($this->getProductUrlKey());
        }
        // Hier noch die category rausfinden
        $reposCategory = $this->getDoctrine()->getRepository(Category::class);
        $category = $reposCategory->findOneBy(array('url_key' => $this->_getUrlKey(), 'level' => $this->_getCategoryLevel()));

        $isCat = false;
        foreach ($this->_product->getCategories() as $_category) {
            if ($_category == $category) {
                $isCat = true;
                break;
            }
        }

        if (!$isCat) {
            if ($category != NULL) {
                $this->_product->addCategory($category);
            }
        }

        $em->persist($this->_product);
        $em->flush();
    }

    protected function getProductUrlKey() {
        $name = $this->_getName() != '' ? str_replace(chr(25), "", utf8_encode($this->_getName())) : '';
        $name = str_replace(' ', '', strtolower(preg_replace('/[^a-z0-9 ]/i', '', $name)));

        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $isNotFound = FALSE;
        $url_key = $name;
        $i = 0;
        while (!$isNotFound) {
            $url_key_found = FALSE;
            foreach ($products as $product) {
                if ($product->getUrlKey() == $url_key) {
                    $url_key = $name . $i++;
                    $url_key_found = TRUE;
                    break;
                }
            }
            if (!$url_key_found) {
                $isNotFound = TRUE;
            }
        }
        return $url_key;
    }

    protected function getProduct() {
        return $this->_product;
    }

    protected function setProductDescription() {

        $reposLanguage = $this->getDoctrine()->getRepository(Language::class);
        $language = $reposLanguage->findOneBy(array('short_name' => $this->getLanguageLocale()));

        $reposProductDescription = $this->getDoctrine()->getRepository(Product\Description::class);
        $productDescription = $reposProductDescription->findOneBy(
                array('product' => $this->getProduct(),
                    'lang' => $language
        ));

        if (!$productDescription) {
            $productDescription = new Description();
        }
        $productDescription->setLang($language);
        $productDescription->setName($this->_getName() != '' ? str_replace(chr(25), "", utf8_encode($this->_getName())) : '');
        $productDescription->setLongText($this->_getDescription() != '' ? str_replace(chr(25), "", utf8_encode($this->_getDescription())) : '');
        $productDescription->setShortText($this->_getShortDescription() != '' ? str_replace(chr(25), "", utf8_encode($this->_getShortDescription())) : '');
        $productDescription->setIndicies($this->_getIndicies() != '' ? str_replace(chr(25), "", utf8_encode($this->_getIndicies())) : '');
        $productDescription->setAccessoires($this->_getAccessoires() != '' ? str_replace(chr(25), "", utf8_encode($this->_getAccessoires())) : '');
        $productDescription->setProduct($this->getProduct());
        $em = $this->getDoctrine()->getManager();
        $em->persist($productDescription);
        $em->flush();
    }

    protected function setImages($serviceCommon) {

        $_dir = $this->get('kernel')->getRootDir() . '/../public/media/import/images/' . $this->_getSku() . '/';
        if (!is_dir($_dir)) {
            return;
        }
        if ($handle = opendir($_dir)) {
            $reposSize = $this->getDoctrine()->getRepository(Product\Image\Size::class);
            $sizes = $reposSize->findAll();
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $this->setOriginalFile($_dir, $file);
                $_file_source = $this->getOriginalFileSource();
                if (mime_content_type($_file_source) == image_type_to_mime_type(IMAGETYPE_JPEG) ||
                        mime_content_type($_file_source) == image_type_to_mime_type(IMAGETYPE_PNG) ||
                        mime_content_type($_file_source) == image_type_to_mime_type(IMAGETYPE_GIF)) {

                    foreach ($sizes as $size) {
                        $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
                        $this->saveImage($size, $serviceCommon);
                        $this->get('helper.imageresizer')->resizeImage($this->getOriginalFileSource(), $_targetDir, $this->getTargetName(), $height = $size->getHeight());
                    }
                }
            }
        }
    }

    protected function deleteImagesNotAllowed() {

        $reposImage = $this->getDoctrine()->getRepository(Product\Image::class);
        $images = $reposImage->findAll();
        foreach ($images as $image) {
            $this->_arr_deleteFile["" . $image->getName()] = $image->getName();
        }

        $reposSize = $this->getDoctrine()->getRepository(Product\Image\Size::class);
        $sizes = $reposSize->findAll();
        foreach ($sizes as $size) {
            $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
            if (is_dir($_targetDir)) {
                if ($handle = opendir($_targetDir)) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file == '.' || $file == '..') {
                            continue;
                        }
                        unset($this->_arr_deleteFile["" . $file]);
                    }
                }
            }
        }

        foreach ($this->_arr_deleteFile as $filename) {
            $reposImage = $this->getDoctrine()->getRepository(Product\Image::class);
            $image = $reposImage->findOneByName($filename);
            if ($image) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($image);
                $em->flush();
            }
        }
    }

    protected function saveImage($size, $serviceCommon) {
        $new_filename = $serviceCommon->getImageName($this->_getSku(), $this->getOriginalName(), $size->getName());
        $this->_arr_targetFile['file'] = $new_filename . '.' . $this->getOriginalFileExtension();
        $this->_arr_targetFile['name'] = $new_filename;
        // prüfe ob in tabelle existiert
        $reposImage = $this->getDoctrine()->getRepository(Product\Image::class);
        $image = $reposImage->findOneByName($this->_arr_targetFile['file']);
        if (!$image) {
            $image = new Image();
        }

        $image->setName($this->_arr_targetFile['file']);
        $image->setOriginalName($this->getOriginalFile());
        $image->setMimetyp(mime_content_type($this->getOriginalFileSource()));
        if (strpos($this->getOriginalFile(), '-default') === false) {
            $image->setIsDefault(false);
        } else {
            $image->setIsDefault(true);
        }
        $image->setSize($size);
        $image->setProduct($this->getProduct());

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

//
    }

    protected function setPrice() {

        $reposPrice = $this->getDoctrine()->getRepository(Price::class);
        $price = $reposPrice->findOneBy(
                array('product' => $this->getProduct(),
                )
        );
        if (!$price) {
            $price = new Price();
        }
        $price->setValue($this->_getPriceLow() != '' ? $this->_getPriceLow() : '0');
        $price->setProduct($this->getProduct());

//
//       //        $price->setSale($this->_getPrice() != '' ? $this->_getPrice() : '0');
//        $price->setWeek($this->_getPriceWeek() != '' ? $this->_getPriceWeek() : '0');
//        $price->setWeekend(0);
//        $price->setWeek3(0);
//        $price->setWeek4(0);
//        $price->setDay(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($price);
        $em->flush();
    }

    protected function setOriginalFile($_dir, $file) {
        $this->_originalFile['file'] = $file;
        $arr_file = explode('.', $file);
        $this->_originalFile['extension'] = $arr_file[count($arr_file) - 1];
        unset($arr_file[count($arr_file) - 1]);
        $this->_originalFile['name'] = implode('.', $arr_file);
        $this->_originalFile['dir'] = $_dir;
    }

    protected function getOriginalFile() {
        return $this->_originalFile['file'];
    }

    protected function getOriginalFileSource() {
        return $this->_originalFile['dir'] . $this->_originalFile['file'];
    }

    protected function getOriginalName() {
        return $this->_originalFile['name'];
    }

    protected function getOriginalFileExtension() {
        return $this->_originalFile['extension'];
    }

    protected function getOriginalDirection() {
        return $this->_originalFile['dir'];
    }

    protected function getTargetFile() {
        return $this->_arr_targetFile['file'];
    }

    protected function getTargetName() {
        return $this->_arr_targetFile['name'];
    }

    protected function _getTyp() {
        return substr($this->_getSku(), 0, 3);
    }

    protected function _getSku() {
        $data = $this->getColumn('sku');
        $data = explode('_', $this->getCsvData($data[0]));
        unset($data[count($data) - 1]);
        return implode('_', $data);
    }

    protected function _getName() {
        $data = $this->getColumn('name');
        $result = '';
        foreach ($data as $_value) {
            if ($this->getCsvData($_value) != '') {
                if ($result != '') {
                    $result .= ' ' . $this->getCsvData($_value);
                } else {
                    $result = $this->getCsvData($_value);
                }
            }
        }
        return $result;
    }

    protected function _getDescription() {
        $data = $this->getColumn('description');
        $html = '<p>';
        $html .= $this->getCsvData($data[0]);
        $html .= '<br/><br />';
        $html .= '<strong>' . utf8_decode($this->getLanguage('set_location')) . ' </strong>';
        $html .= $this->getCsvData($data[1]);
        $html .= '<br/><br />';
        $html .= '<strong>' . utf8_decode($this->getLanguage('age_utilisation')) . ' </strong>';
        $html .= $this->getCsvData($data[2]);
        $html .= '</p>';
        return $html;
    }

    protected function _getShortDescription() {
        $data = $this->getColumn('short_description');
        return $this->getListe($this->getCsvData($data[0]));
    }

    /**
     *
     * @return string
     */
    protected function _getIndicies() {
        $data = $this->getColumn('indicies');
        $html = '<p><strong>';
        $html .= $this->getLanguage('dimensions');
        $html .= '</p></strong>';
        $html .= $this->getListe($this->getCsvData($data[0]));
        $html .= '<br />';
        $html .= '<p><strong>';
        $html .= $this->getLanguage('poids');
        $html .= '</p></strong>';
        $html .= $this->getListe($this->getCsvData($data[1]));
        $html .= '<br />';
        return $html;
    }

    protected function _getUrlKey() {
        $data = $this->getColumn('url_key');
        return $this->getListe($this->getCsvData($data[0]));
    }

    protected function _getCategoryLevel() {
        $data = $this->getColumn('category_level');
        return $this->getListe($this->getCsvData($data[0]));
    }

    protected function _getAccessoires() {
        $data = $this->getColumn('accessoires');
        return $this->getListe($this->getCsvData($data[0]));
    }

    protected function _getPrice() {
        $data = $this->getColumn('price');
        return $this->getCsvData($data[0]);
    }

    protected function _getPriceLow() {
        $data = $this->getColumn('price_low');
        return $this->getCsvData($data[0]);
    }

    protected function _setAdditionals() {

        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneBySku($this->_getSku());

        $reposProductGroup = $this->getDoctrine()->getRepository(Map\ProductGroup::class);
        $em = $this->getDoctrine()->getManager();

        $arr_sku = $this->_getSkuFromAddtionals();
        foreach ($arr_sku as $sku) {
            $addproduct = $reposProduct->findOneBySku($sku);
            $productGroup = $reposProductGroup->findOneBy(
                    array('parent' => $product->getId(),
                        'child' => $addproduct->getId()
                    )
            );
            if (!$productGroup) {
                $productGroup = new ProductGroup();
            }
            $productGroup->setParent($product->getId());
            $productGroup->setChild($addproduct->getId());
            $em->persist($productGroup);
            $em->flush();
        }
    }

    protected function _getSkuFromAddtionals() {
        $arr_add = array();
        $data = $this->getColumn('addproduct');
        $arr_data = explode(',', $this->getCsvData($data[0]));
        foreach ($arr_data as $data) {
            $_arr_data = explode('_', trim($data));
            unset($_arr_data[count($_arr_data) - 1]);
            $arr_add[] = implode('_', $_arr_data);
        }
        return $arr_add;
    }

    protected function getColumn($id) {
        return explode(',', $this->getDesignation($id));
    }

    protected function getDesignation($id) {
        return $this->_arr_designation[$id];
    }

    protected function setCsvData($data) {
        return $this->_csv_data = $data;
    }

    protected function getCsvData($id) {
        return $this->_csv_data[$id];
    }

    protected function setDesignation() {
        $this->_arr_designation = array(
            'sku' => '0',
            'name' => '6,7',
            'addproduct' => '3',
            'description' => '9,10,11',
            'short_description' => '12',
            'indicies' => '13,14',
            'accessoires' => '15',
            'price' => '16',
            'price_low' => '17',
            'url_key' => '2',
            'category_level' => '18',
        );
    }

    protected function setTitlesDescription() {
        $this->_arr_language = array(
            'set_location' => array(
                'fr' => 'Set de location:',
                'de' => 'Mietset:',
                'en' => 'Set of rent:',
                'es' => 'Set de alquiler:',
                'it' => 'Set di locazione:'
            ),
            'age_utilisation' => array(
                'fr' => 'Âge utilisation:',
                'de' => 'Benutzungsalter:',
                'en' => 'Age of use:',
                'es' => 'Edad de utilización:',
                'it' => 'età di utilizzazione:'
            ),
            'dimensions' => array(
                'fr' => 'Dimensions',
                'de' => 'Abmessungen',
                'en' => 'Dimensions',
                'es' => 'Dimensiones',
                'it' => 'Dimensioni'
            ),
            'poids' => array(
                'fr' => 'Poids',
                'de' => 'Gewicht',
                'en' => 'Weight',
                'es' => 'Peso',
                'it' => 'Peso'
            ),
        );
    }

    protected function setLanguageLocale() {
        $arr_data = explode('_', $this->_csv_data[$this->getDesignation('sku')]);
        $this->_locale = strtolower($arr_data[count($arr_data) - 1]);
    }

    protected function getLanguageLocale() {
        return $this->_locale;
    }

    protected function getLanguage($key) {
        return $this->_arr_language[$key][$this->getLanguageLocale()];
    }

    protected function getListe($text) {

        if (strpos($text, '•') !== FALSE) {
            return $this->getUlListe(explode('•', trim($text)));
        } else if (strpos($text, "\n") !== FALSE) {
            return $this->getUlListe(explode("\n", trim($text)));
        } else if ($text != '') {
            return nl2br($text);
        } else {
            return $this->_getName();
        }
    }

    protected function getUlListe($arr_text) {
        $html = '<ul>';
        foreach ($arr_text as $text) {
            if (trim($text) != '') {
                $html .= '<li>' . trim($text) . '</li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

}
