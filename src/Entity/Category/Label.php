<?php

namespace App\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Category;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="category_label")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Category\Label")
 */
class Label {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="labels")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="labels")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Label
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set lang
     *
     * @param \App\Entity\Language $lang
     *
     * @return Label
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
     * Set category
     *
     * @param \App\Entity\Category $category
     *
     * @return Label
     */
    public function setCategory(\App\Entity\Category $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \App\Entity\Category
     */
    public function getCategory() {
        return $this->category;
    }

}
