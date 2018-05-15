<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Common As ServiceCommon;
use App\Service\Product As ServiceProduct;
use App\Service\Navigation As ServiceNavigation;
use App\Service\ImageResizer As ServiceImageResizer;
use App\Entity\Product;
use App\Entity\Product\Description;
use App\Entity\Product\Image;
use App\Entity\Map\ProductAdditional;
use App\Entity\Price;

class AdminController extends Controller {

    /**
     * @Template()
     * @Route("/", name="admin_index")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Template()
     * @Route("/login/", name="admin_login")
     */
    public function loginAction() {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function logoutAction() {

    }

    /**
     * @Template()
     * @Route("/new", name="admin_product_new")
     */
    public function newAction(ServiceCommon $serviceCommon) {
        $reposProductTyp = $this->getDoctrine()->getRepository(\App\Entity\Product\Typ::class);
        $productTyps = $reposProductTyp->findAll();
        $navigation = $serviceCommon->getNavigation('category');
        $reposLanguage = $this->getDoctrine()->getRepository(\App\Entity\Language::class);
        $languages = $reposLanguage->findAll();

        return array(
            'producttyps' => $productTyps,
            'navigation' => $navigation,
            'languages' => $languages
        );
    }

    /**
     * @Template()
     * @Route("/productnewsave", name="admin_product_new_save")
     */
    public function productNewSaveAction(Request $request) {
        $productRepos = $this->getDoctrine()->getRepository(Product::class);
        $requests = $request->request->get('product');

        $product = $productRepos->findOneBy(array('sku' => $requests['sku']));
        $reposProductTyp = $this->getDoctrine()->getRepository(\App\Entity\Product\Typ::class);
        $productTyp = $reposProductTyp->findOneBy(array('short_name' => $requests['typ']));
        $em = $this->getDoctrine()->getManager();
        $status = false;

        if (!$product) {
            $status = true;
            $product = new Product();
            $product->setTyp($productTyp);
            $product->setSku($requests['sku']);
            $product->setStatus(false);
            $product->setSale($requests['sale']);
            $em->persist($product);
            $em->flush();

            // add to category
            $reposCategory = $this->getDoctrine()->getRepository(\App\Entity\Category::class);
            $arr_category = explode(',', $requests['category']);
            foreach ($arr_category as $categoryid) {
                $category = $reposCategory->findOneById($categoryid);
                $product->addCategory($category);
                $em->persist($product);
                $em->flush();
            }

            // product price
            $price = new Price();
            $price->setProduct($product);
            $price->setValue($requests['price']);
            $em->persist($price);
            $em->flush();
            // product description
            $reposLanguage = $this->getDoctrine()->getRepository(\App\Entity\Language::class);
            $languages = $reposLanguage->findAll();
            foreach ($languages as $language) {
                $productDescription = new Description();
                $productDescription->setLang($language);
                $productDescription->setProduct($product);
                $productDescription->setName($requests['name'][$language->getShortName()]);
                $productDescription->setLongText('');
                $productDescription->setShortText('');
                $productDescription->setIndicies('');
                $productDescription->setAccessoires('');
                $em->persist($productDescription);
                $em->flush();
            }
        }
        return new JsonResponse(array('status' => $status, 'product_id' => $product->getId()));
    }

    /**
     * @Template()
     * @Route("/list", name="admin_product_list")
     */
    public function listAction() {
        $reposProductTyp = $this->getDoctrine()->getRepository(\App\Entity\Product\Typ::class);
        $productTyp = $reposProductTyp->findOneBy(array('short_name' => 'SIP'));
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $products = $reposProduct->findBy(array('typ' => $productTyp));
        return array(
            'products' => $products
        );
    }

    /**
     * @Template()
     * @Route("/search/product", name="admin_product_search")
     */
    public function productSearchAction(ServiceCommon $serviceCommon, ServiceNavigation $serviceNavigation) {
        return $serviceCommon->getSearchData($serviceNavigation);
    }

    /**
     * @Template()
     * @Route("/detail/", name="admin_product_detail_noid")
     * @Route("/detail/{id}/", name="admin_product_detail")
     * @Route("/detail/{id}/{lang}/", defaults={"lang" = "fr"}, name="admin_product_detail_lang")
     */
    public function detailAction($id, $lang = 'fr', ServiceCommon $serviceCommon, ServiceProduct $serviceProduct) {
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($id);
        $children = $serviceProduct->getChildrenByRealParent($id);
        $reposLanguage = $this->getDoctrine()->getRepository(\App\Entity\Language::class);
        $languages = $reposLanguage->findAll();
        $navigation = $serviceCommon->getNavigation('category');
        return array(
            'product' => $product,
            'lang' => $lang,
            'children' => $children,
            'languages' => $languages,
            'navigation' => $navigation
        );
    }

    /**
     * @Template()
     * @Route("/productdelete/{id}/", name="admin_product_delete")
     */
    public function productDeleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneById($id);

        // Description
        foreach ($product->getDescriptions() as $description) {
            $description->removeDescription($description);
        }

        // Price
        $price = $this->getDoctrine()->getRepository(Price::class)->findOneBy(array('product' => $product));
        if (!$price) {
            $em->remove($price);
            $em->flush();
        }

        $reposImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
        $images = $reposImage->findBy(array('product' => $children));
        foreach ($images as $image) {
            $fs = new Filesystem();
            $fs->remove($this->get('kernel')->getRootDir() . '/../public/' . $image->getSize()->getPath() . $image->getName());
            $em->remove($image);
            $em->flush();
        }

        $reposDescription = $this->getDoctrine()->getRepository(\App\Entity\Product\Description::class);
        $descriptions = $reposDescription->findBy(array('product' => $children));
        foreach ($descriptions as $description) {
            $em->remove($description);
            $em->flush();
        }

        $reposPrice = $this->getDoctrine()->getRepository(Price::class);
        $prices = $reposPrice->findBy(array('product' => $children));
        foreach ($prices as $price) {
            $em->remove($price);
            $em->flush();
        }
        // delete children
        $em->remove($children);
        $em->flush();

        $reposProductAdditional = $this->getDoctrine()->getRepository(\App\Entity\Map\ProductAdditional::class);
        $productAdditional = $reposProductAdditional->findOneBy(array('parent' => $product_id, 'children' => $children_id));
        $em->remove($productAdditional);
        $em->flush();
    }

    /**
     * @Template()
     * @Route("/status", name="admin_status")
     */
    public function statusAction(Request $request) {
        $product_id = $request->request->get('product_id');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);
        if ($product->getStatus()) {
            $product->setStatus(false);
        } else {
            $product->setStatus(true);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return new JsonResponse(array('status' => $product->getStatus()));
    }

    /**
     * @Template()
     * @Route("/categorysave/", name="admin_category")
     */
    public function saveCategoryAction(Request $request) {
        $product_id = $request->request->get('product_id');
        $category_ids = $request->request->get('category_ids');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);
        $em = $this->getDoctrine()->getManager();
        foreach ($product->getCategories() as $category) {
            $product->removeCategory($category);
            $em->persist($product);
            $em->flush();
        }

        $reposCategory = $this->getDoctrine()->getRepository(\App\Entity\Category::class);
        $arr_category = explode(',', $category_ids);
        foreach ($arr_category as $categoryid) {
            $category = $reposCategory->findOneById($categoryid);
            $product->addCategory($category);
            $em->persist($product);
            $em->flush();
        }

        $html = '';
        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/pricetextsave/", name="admin_price_text_save")
     */
    public function savePriceTextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');

        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $reposLanguage = $this->getDoctrine()->getRepository(\App\Entity\Language::class);

        $product = $reposProduct->findOneById($product_id);
        $language = $reposLanguage->findOneBy(array('short_name' => $lang));
        $arr_fields = array(
            'descriptionname' => '\\App\\Entity\\Product\\Description',
            'tinymelongtext' => '\\App\\Entity\\Product\\Description',
            'tinymeshorttext' => '\\App\\Entity\\Product\\Description',
            'tinymeindicies' => '\\App\\Entity\\Product\\Description',
        );

        $arr_data = $request->request->all();
        $html = '';
        foreach ($arr_data as $name => $data) {
            $field = $name;
            $field = explode('_', $field);

            if ($field[0] == 'descriptionname') {
                $reposCommon = $this->getDoctrine()->getRepository(Description::class);
                $productDescription = $reposCommon->findOneBy(array('lang' => $language, 'product' => $product));
                $productDescription->setName($data);
                $em->persist($productDescription);
                $em->flush();
            } else if ($field[0] == 'tinymeshorttext') {
                $crawler = new Crawler($data);
                $crawler = $crawler->filter('body');
                $data = trim($crawler->html());
                $reposCommon = $this->getDoctrine()->getRepository(Description::class);
                $productDescription = $reposCommon->findOneBy(array('lang' => $language, 'product' => $product));
                $productDescription->setShortText($data);
                $em->persist($productDescription);
                $em->flush();
            } else if ($field[0] == 'tinymelongtext') {
                $crawler = new Crawler($data);
                $crawler = $crawler->filter('body');
                $data = trim($crawler->html());
                $reposCommon = $this->getDoctrine()->getRepository(Description::class);
                $productDescription = $reposCommon->findOneBy(array('lang' => $language, 'product' => $product));
                $productDescription->setLongText($data);
                $em->persist($productDescription);
                $em->flush();
            } else if ($field[0] == 'tinymeindicies') {
                $crawler = new Crawler($data);
                $crawler = $crawler->filter('body');
                $data = trim($crawler->html());
                $reposCommon = $this->getDoctrine()->getRepository(Description::class);
                $productIndicies = $reposCommon->findOneBy(array('lang' => $language, 'product' => $product));
                $productIndicies->setIndicies($data);
                $em->persist($productIndicies);
                $em->flush();
            } else if ($field[0] == 'price') {
                $price = $product->getPrice();
                $price->setValue($data);
                $em->persist($price);
                $em->flush();
            }
        }

        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/save/", name="admin_description_save")
     */
    public function saveDescriptionAction(Request $request) {
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $reposLanguage = $this->getDoctrine()->getRepository(\App\Entity\Language::class);

        $product = $reposProduct->findOneById($product_id);
        $language = $reposLanguage->findOneBy(array('short_name' => $lang));

        $arr_fields = array(
            'descriptionname' => 'Description',
            'price' => 'Price',
        );
        $arr_data = $request->request->all();
        foreach ($arr_data as $names => $data) {
            $arr_name = explode('_', $names);
            foreach ($arr_name as $name) {
                foreach ($arr_fields as $field => $entity) {
                    $reposCommon = $this->getDoctrine()->getRepository($entity . '::class');
                }
            }
        }
    }

    /**
     * @Template()
     * @Route("/upload/", name="admin_file_upload")
     */
    public function upload(Request $request, ServiceCommon $serviceCommon, ServiceImageResizer $serviceImageResizer) {

        $files = $request->files->get("sendimages");
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);
        $images = new ArrayCollection();
        $images = $this->saveUploadImage($files, $product, '', $serviceCommon, $serviceImageResizer);
        $html = $this->renderView('admin/admin/image.html.twig', array(
            'images' => $images,
            'product' => $product,
            'lang' => $lang,
                )
        );

        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/delete/", name="admin_file_delete")
     */
    public function deleteAction(Request $request) {
        $product_id = $request->request->get('product_id');
        $image_id = $request->request->get('image_id');
        $lang = $request->request->get('lang');
        $em = $this->getDoctrine()->getManager();
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);
        $reposImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
        $image = $reposImage->findOneBy(array('id' => $image_id, 'product' => $product));

        $images = $reposImage->findBy(array('product' => $product, 'original_name' => $image->getOriginalName()));
        foreach ($images as $image) {
            $fs = new Filesystem();
            $fs->remove($this->get('kernel')->getRootDir() . '/../public/' . $image->getSize()->getPath() . $image->getName());
            $em->remove($image);
            $em->flush();
        }

        return new JsonResponse(array('html' => ''));
    }

    /**
     * @Template()
     * @Route("/uploadelement/", name="admin_element_upload")
     */
    public function uploadElement(Request $request, ServiceCommon $serviceCommon, ServiceImageResizer $serviceImageResizer) {

        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $skus = $request->request->get('sku');
        $prices = $request->request->get('price');

        $additionaldescriptions = $request->request->get('sendadditionaldescription');
        $reposLang = $this->getDoctrine()->getRepository(\App\Entity\Language::class);
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $em = $this->getDoctrine()->getManager();
        $error = array();
        $html = '';
        foreach ($skus as $sku) {
            $children = $reposProduct->findOneBySku(trim($sku));
            if ($children) {
                // Fehler meldung
                $message = $this->get('translator')->trans(
                        'Dieses Produkt %sku% existiert bereits', array('%sku%' => $children->getSku())
                );

                return new JsonResponse(array('html' => '', 'error' => $message));
            }
        }
        foreach ($skus as $sku) {
            $children = new Product();
            $reposTyp = $this->getDoctrine()->getRepository(\App\Entity\Product\Typ::class);
            $typ = $reposTyp->findOneBy(array('short_name' => 'ADD'));
            $children->setCategory(NULL);
            $children->setTyp($typ);
            $children->setSku($sku);
            $children->setStatus(true);
            $children->setSale(0);
            $em->persist($children);
            $em->flush();

            $files = $request->files->get("sendadditionalimages");
            $images = new ArrayCollection();
            if ($files) {
                $images = $this->saveUploadImage($files[$sku], $children, '', $serviceCommon, $serviceImageResizer);
            }

            $productAdditional = new ProductAdditional();
            $productAdditional->setChildren($children->getId());
            $productAdditional->setParent($product_id);
            $em->persist($productAdditional);
            $em->flush();

            // Add Price
            $entityPrices = new ArrayCollection();
            foreach ($skus as $sku) {
                $price = new Price();
                $price->setProduct($children);
                $price->setValue($prices[$sku]);
                $em->persist($price);
                $em->flush();
                $entityPrices[] = $price;
            }

            // Add description
            $entityDescriptions = new ArrayCollection();
            foreach ($additionaldescriptions[$sku] as $lang_id => $additionaldescription) {
                $language = $reposLang->findOneById($lang_id);
                $description = new Description();
                $description->setLang($language);
                $description->setProduct($children);
                $description->setName($additionaldescription);
                $description->setLongText($additionaldescription);
                $description->setShortText($additionaldescription);
                $description->setIndicies($additionaldescription);
                $description->setAccessoires($additionaldescription);
                $em->persist($description);
                $em->flush();
                $entityDescriptions[] = $description;
            }


            $html .= $this->renderView('admin/admin/children.html.twig', array(
                'lang' => $lang,
                'images' => $images,
                'descriptions' => $entityDescriptions,
                'prices' => $entityPrices,
                'child' => $children
                    )
            );
        }
        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/deleteelement/", name="admin_element_delete")
     */
    public function deleteElement(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $children_id = $request->request->get('children_id');
        $lang = $request->request->get('lang');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $children = $reposProduct->findOneById($children_id);

        $reposImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
        $images = $reposImage->findBy(array('product' => $children));
        foreach ($images as $image) {
            $fs = new Filesystem();
            $fs->remove($this->get('kernel')->getRootDir() . '/../public/' . $image->getSize()->getPath() . $image->getName());
            $em->remove($image);
            $em->flush();
        }

        $reposDescription = $this->getDoctrine()->getRepository(\App\Entity\Product\Description::class);
        $descriptions = $reposDescription->findBy(array('product' => $children));
        foreach ($descriptions as $description) {
            $em->remove($description);
            $em->flush();
        }

        $reposPrice = $this->getDoctrine()->getRepository(Price::class);
        $prices = $reposPrice->findBy(array('product' => $children));
        foreach ($prices as $price) {
            $em->remove($price);
            $em->flush();
        }
        // delete children
        $em->remove($children);
        $em->flush();

        $reposProductAdditional = $this->getDoctrine()->getRepository(\App\Entity\Map\ProductAdditional::class);
        $productAdditional = $reposProductAdditional->findOneBy(array('parent' => $product_id, 'children' => $children_id));
        $em->remove($productAdditional);
        $em->flush();


        return new JsonResponse(array('html' => ''));
    }

    /**
     * @Template()
     * @Route("/imagedefault/", name="admin_image_default")
     */
    public function setImageDefaultAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $image_id = $request->request->get('image_id');
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);

        $reposProductImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
        $productImages = $reposProductImage->findBy(array('product' => $product, 'is_default' => 1));
        foreach ($productImages as $productImage) {
            $productImage->setIsDefault(false);
            $em->persist($productImage);
            $em->flush();
        }

        $productImage = $reposProductImage->findOneById($image_id);
        $productImages = $reposProductImage->findBy(array('original_name' => $productImage->getOriginalName()));
        foreach ($productImages as $productImage) {
            $productImage->setIsDefault(true);
            $em->persist($productImage);
            $em->flush();
        }
        return new JsonResponse(array('html' => ''));
    }

    /**
     * @Template()
     * @Route("/imageupdate/", name="admin_image_update")
     */
    public function setImageUpdateAction(Request $request, ServiceCommon $serviceCommon, ServiceImageResizer $serviceImageResizer) {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $files = $request->files->get("sendimages");
        $reposProduct = $this->getDoctrine()->getRepository(Product::class);
        $product = $reposProduct->findOneById($product_id);
        $arr_html = array();
        foreach ($files as $image_id => $file) {
            $arr_image_id[] = $image_id;
            $reposProductImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
            $productImage = $reposProductImage->findOneById($image_id);
            $productImages = $reposProductImage->findBy(array('original_name' => $productImage->getOriginalName()));
            foreach ($productImages as $productImage) {
                $fs = new Filesystem();
                $fs->remove($this->get('kernel')->getRootDir() . '/../public/' . $productImage->getSize()->getPath() . $productImage->getName());
            }
            $productImage = $reposProductImage->findOneById($image_id);
            $priorOriginalName = $productImage->getOriginalName();
            $arr_files = array();
            $arr_files[] = $file;
            $images = $this->saveUploadImage($arr_files, $product, $priorOriginalName, $serviceCommon, $serviceImageResizer);
            $arr_html[$image_id] = $this->renderView('admin/admin/image.html.twig', array(
                'images' => $images,
                'product' => $product,
                'lang' => $lang,
                    )
            );
        }
        return new JsonResponse(array('arr_html' => $arr_html));
    }

    /**
     * @Template()
     * @Route("/elementupdate/", name="admin_element_update")
     */
    public function setElementUpdateAction(Request $request, ServiceCommon $serviceCommon, ServiceImageResizer $serviceImageResizer) {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $files = $request->files->get("sendimages");
        $productdescriptions = $request->request->get('productdescription');
        $productprices = $request->request->get('productprice');

        $html = '';
        $reposProductDescription = $this->getDoctrine()->getRepository(\App\Entity\Product\Description::class);
        foreach ($productdescriptions as $child_id => $arr_productdescription) {
            $reposProduct = $this->getDoctrine()->getRepository(Product::class);
            $children = $reposProduct->findOneBy(array('id' => $child_id));

            $entityDescriptions = new ArrayCollection();
            foreach ($arr_productdescription as $productdesc_id => $productdescription) {
                $description = $reposProductDescription->findOneBy(array('id' => $productdesc_id));
                $description->setName($productdescription);
                $description->setLongText($productdescription);
                $description->setShortText($productdescription);
                $description->setIndicies($productdescription);
                $description->setAccessoires($productdescription);
                $em->persist($description);
                $em->flush();
                $entityDescriptions[] = $description;
            }

            $entityPrices = new ArrayCollection();
            foreach ($productprices[$child_id] as $product_price_id => $productprice) {
                $price = $this->getDoctrine()->getRepository(\App\Entity\Price::class)->findOneById($product_price_id);
                $price->setValue($productprice);
                $em->persist($price);
                $em->flush();
                $entityPrices[] = $price;
            }


            $images = new ArrayCollection();

            if (isset($files[$child_id])) {
                if ($children->getImages()) {
                    $reposImage = $this->getDoctrine()->getRepository(\App\Entity\Product\Image::class);
                    $images = $reposImage->findBy(array('product' => $children));
                    foreach ($images as $image) {
                        $fs = new Filesystem();
                        $fs->remove($this->get('kernel')->getRootDir() . '/../public/' . $image->getSize()->getPath() . $image->getName());
                        $em->remove($image);
                        $em->flush();
                    }
                }
                $images = $this->saveUploadImage($files[$child_id], $children, '', $serviceCommon, $serviceImageResizer);
            } else {
                if ($children->getImages()) {
                    $images = $children->getImages();
                }
            }

            $html .= $this->renderView('admin/admin/children.html.twig', array(
                'lang' => $lang,
                'images' => $images,
                'descriptions' => $entityDescriptions,
                'prices' => $entityPrices,
                'child' => $children
                    )
            );
        }
        return new JsonResponse(array('html' => $html));
    }

    /**
     * @Template()
     * @Route("/uploadpdfaccessoires/", name="admin_pdf_accessoires")
     */
    public function uploadPdfAccessoiresAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $lang = $request->request->get('lang');
        $file = $request->files->get("pdf");
        if ($file->getClientOriginalExtension() != '') {
            $reposProduct = $this->getDoctrine()->getRepository(Product::class);
            $product = $reposProduct->findOneById($product_id);
            foreach ($product->getDescriptions() as $description) {
                if ($description->getLang()->getShortName() != NULL && $description->getLang()->getShortName() == $lang) {
                    $filename = 'PDF_' . $product->getSku();
                    $path = $this->get('kernel')->getRootDir() . "/../public/media/import/images/" . $product->getSku() . "/";
                    $description->setAccessoires($filename);
                    $em->persist($description);
                    $em->flush();
                    $filename = 'PDF_' . $product->getSku() . '.' . $file->getClientOriginalExtension();
                    $file->move($path, $filename);
                    $html = $this->renderView('admin/admin/pdfaccessoires.html.twig', array(
                        'lang' => $lang,
                        'product' => $product
                            )
                    );
                    return new JsonResponse(array('html' => $html));
                }
            }
        }
    }

    /**
     *
     * @param file $files
     * @param product $product
     * @return int
     */
    protected function saveUploadImage($files, $product, $priorOriginalName = '', $serviceCommon, $serviceImageResizer) {
        $newNodes = 0;
        $images = new ArrayCollection();

        foreach ($files as $file) {
            if ($file->getClientOriginalExtension() != '') {
                $arr_files = explode('.', $file->getClientOriginalName());
                unset($arr_files[count($arr_files) - 1]);
                $org_filename = implode('.', $arr_files);
                $newNodes++;
            }

            $reposSize = $this->getDoctrine()->getRepository(\App\Entity\Product\Image\Size::class);
            $sizes = $reposSize->findAll();
            foreach ($sizes as $key => $size) {
                $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
                $name = $serviceCommon->getImageName($product->getSku(), $org_filename, $size->getName());
                $filename = $name . '.' . $file->getClientOriginalExtension();
                $target_file = $_targetDir . $filename;
                if ($key == 0) {
                    $file->move($_targetDir, $filename);
                    $source_file = $_targetDir . $filename;
                } else {
                    $fs = new Filesystem();
                    $fs->copy($source_file, $target_file);
                }
            }

            foreach ($sizes as $key => $size) {
                $_targetDir = $this->get('kernel')->getRootDir() . '/../public/' . $size->getPath();
                $name = $serviceCommon->getImageName($product->getSku(), $org_filename, $size->getName());
                $filename = $name . '.' . $file->getClientOriginalExtension();
                $target_file = $_targetDir . $filename;
                $serviceImageResizer->resizeImage($target_file, $_targetDir, $name, $height = $size->getHeight());
                $reposImage = $this->getDoctrine()->getRepository(Product\Image::class);
                if ($priorOriginalName != '') {
                    $image = $reposImage->findOneBy(array('product' => $product, 'size' => $size, 'original_name' => $priorOriginalName));
                } else {
                    $image = $reposImage->findOneBy(array('product' => $product, 'size' => $size, 'original_name' => $file->getClientOriginalName()));
                }
                if (!$image) {
                    $image = new Image();
                }
                $image->setName($filename);
                $image->setOriginalName($file->getClientOriginalName());
                $image->setMimetyp($file->getClientMimeType());
                if (strpos($file->getClientOriginalName(), '-default') === false) {
                    if (count($files) == 1 && count($product->getImages()) == 0) {
                        $image->setIsDefault(true);
                    } else {
                        $image->setIsDefault(false);
                    }
                } else {
                    // set all other to false
                    $images = $reposImage->findBy(array('product' => $product, 'size' => $size));
                    foreach ($images as $img) {
                        $img->setIsDefault(false);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($img);
                        $em->flush();
                    }
                    $image->setIsDefault(true);
                }

                // if first one
                $image->setSize($size);
                $image->setProduct($product);
                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $em->flush();
                $images[] = $image;
            }
        }
        return $images;
    }

}
