<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;
use App\Entity\Product;
use App\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="App\Repository\Content")
 */
class Content {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $short_text = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $long_text = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatet_at;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $status = false;

    /**
     * @ORM\Column(name="`order`", type="integer", length=5, options={"default":0})
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="contents")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", mappedBy="contents")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdminUser", inversedBy="contents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", inversedBy="contents")
     * @ORM\JoinTable(name="contents_images")
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Content\Group", inversedBy="contents")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     *
     * @var type category
     */
    private $category;

    /**
     *
     * @var type cat_url_key
     */
    private $cat_url_key;

    public function __construct() {
        $this->categories = new ArrayCollection();
        $this->setUpdatetAt(new \DateTime());
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Content
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set shortText
     *
     * @param string $shortText
     *
     * @return Content
     */
    public function setShortText($shortText) {
        $this->short_text = $shortText;

        return $this;
    }

    /**
     * Get shortText
     *
     * @return string
     */
    public function getShortText() {
        return $this->short_text;
    }

    /**
     * Set longText
     *
     * @param string $longText
     *
     * @return Content
     */
    public function setLongText($longText) {
        $this->long_text = $longText;

        return $this;
    }

    /**
     * Get longText
     *
     * @return string
     */
    public function getLongText() {
        return $this->long_text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Content
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updatetAt
     *
     * @param \DateTime $updatetAt
     *
     * @return Content
     */
    public function setUpdatetAt($updatetAt) {
        $this->updatet_at = $updatetAt;

        return $this;
    }

    /**
     * Get updatetAt
     *
     * @return \DateTime
     */
    public function getUpdatetAt() {
        return $this->updatet_at;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Content
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Content
     */
    public function setOrder($order) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set lang
     *
     * @param \App\Entity\Language $lang
     *
     * @return Content
     */
    public function setLang(\App\Entity\Language $lang = null) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \App\Entity\Language
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Add category
     *
     * @param \App\Entity\Category $category
     *
     * @return Content
     */
    public function addCategory(\App\Entity\Category $category) {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \App\Entity\Category $category
     */
    public function removeCategory(\App\Entity\Category $category) {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return Content
     */
    public function setUser(\App\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add image
     *
     * @param \App\Entity\Image $image
     *
     * @return Content
     */
    public function addImage(\App\Entity\Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \App\Entity\Image $image
     */
    public function removeImage(\App\Entity\Image $image) {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Get images
     *
     * @return Image
     */
    public function getImage() {
        if (count($this->getImages()) > 0) {
            foreach ($this->getImages() as $image) {
                return $image;
            }
        }
        return NULL;
    }

    /**
     * Set group
     *
     * @param \App\Entity\Content\Group $group
     *
     * @return Content
     */
    public function setGroup(\App\Entity\Content\Group $group = null) {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\Entity\Content\Group
     */
    public function getGroup() {
        return $this->group;
    }

    /** Manuelle Anpassungen in dieser Entity * */

    /**
     *
     * @param category $category
     */
    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     *
     * @return category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     *
     * @param string $cat_url_key
     */
    public function setCategoryUrlKey($cat_url_key) {
        $this->cat_url_key = $cat_url_key;
    }

    /**
     *
     * @return string
     */
    public function getCategoryUrlKey() {
        return $this->cat_url_key;
    }

}
