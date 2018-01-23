<?php

namespace App\Entity\Gift;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_text")
 * @ORM\Entity(repositoryClass="App\Repository\Gift\Text")
 */
class Text {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="gifttexts")
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gift", inversedBy="texts")
     * @ORM\JoinColumn(name="gift_id", referencedColumnName="id")
     */
    private $gift;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $description
     *
     * @return Description
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
     * Set language
     *
     * @param \App\Entity\Language $lang
     *
     * @return Text
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
     * Set gift
     *
     * @param \App\Entity\Gift $gift
     *
     * @return Text
     */
    public function setGift(\App\Entity\Gift $gift = null) {
        $this->gift = $gift;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \App\Entity\Gift
     */
    public function getGift() {
        return $this->gift;
    }

}
