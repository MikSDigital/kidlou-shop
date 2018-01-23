<?php

namespace App\Entity\Nivoslider;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="nivoslider_item")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Nivoslider\Item")
 */
class Item {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title2 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title3 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image2 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image3 = null;

    /**
     * @ORM\Column(type="smallint", length=6, name="`order`")
     */
    private $order = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="nivosliders")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Nivoslider", inversedBy="items")
     * @ORM\JoinColumn(name="nivoslider_id", referencedColumnName="id")
     */
    private $nivoslider;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatet_at;

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
     * @return Item
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
     * Set title2
     *
     * @param string $title2
     *
     * @return Item
     */
    public function setTitle2($title2) {
        $this->title2 = $title2;

        return $this;
    }

    /**
     * Get title2
     *
     * @return string
     */
    public function getTitle2() {
        return $this->title2;
    }

    /**
     * Set title3
     *
     * @param string $title3
     *
     * @return Item
     */
    public function setTitle3($title3) {
        $this->title3 = $title3;

        return $this;
    }

    /**
     * Get title3
     *
     * @return string
     */
    public function getTitle3() {
        return $this->title3;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return Item
     */
    public function setLink($link) {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Item
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set image2
     *
     * @param string $image2
     *
     * @return Item
     */
    public function setImage2($image2) {
        $this->image2 = $image2;

        return $this;
    }

    /**
     * Get image2
     *
     * @return string
     */
    public function getImage2() {
        return $this->image2;
    }

    /**
     * Set image3
     *
     * @param string $image3
     *
     * @return Item
     */
    public function setImage3($image3) {
        $this->image3 = $image3;

        return $this;
    }

    /**
     * Get image3
     *
     * @return string
     */
    public function getImage3() {
        return $this->image3;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Item
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
     * Set description
     *
     * @param string $description
     *
     * @return Item
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Item
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
     * @return Item
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
     * Set lang
     *
     * @param \App\Entity\Language $lang
     *
     * @return Item
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
     * Set nivoslider
     *
     * @param \App\Entity\Nivoslider $nivoslider
     *
     * @return Item
     */
    public function setNivoslider(\App\Entity\Nivoslider $nivoslider = null) {
        $this->nivoslider = $nivoslider;

        return $this;
    }

    /**
     * Get nivoslider
     *
     * @return \App\Entity\Nivoslider
     */
    public function getNivoslider() {
        return $this->nivoslider;
    }

}
