<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="inbox")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\Inbox")
 */
class Inbox {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $email_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $from_name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $from_address;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $to_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $to_address;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $cc_name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $cc_address;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $bcc_name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $bcc_address;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text_plain;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text_html;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file_paths;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file_names;

    /**
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $parent_id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set emailId
     *
     * @param integer $emailId
     *
     * @return Inbox
     */
    public function setEmailId($emailId) {
        $this->email_id = $emailId;

        return $this;
    }

    /**
     * Get emailId
     *
     * @return integer
     */
    public function getEmailId() {
        return $this->email_id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Inbox
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set fromName
     *
     * @param string $fromName
     *
     * @return Inbox
     */
    public function setFromName($fromName) {
        $this->from_name = $fromName;

        return $this;
    }

    /**
     * Get fromName
     *
     * @return string
     */
    public function getFromName() {
        return $this->from_name;
    }

    /**
     * Set fromAddress
     *
     * @param string $fromAddress
     *
     * @return Inbox
     */
    public function setFromAddress($fromAddress) {
        $this->from_address = $fromAddress;

        return $this;
    }

    /**
     * Get fromAddress
     *
     * @return string
     */
    public function getFromAddress() {
        return $this->from_address;
    }

    /**
     * Set toName
     *
     * @param string $toName
     *
     * @return Inbox
     */
    public function setToName($toName) {
        $this->to_name = $toName;

        return $this;
    }

    /**
     * Get toName
     *
     * @return string
     */
    public function getToName() {
        return $this->to_name;
    }

    /**
     * Set toAddress
     *
     * @param string $toAddress
     *
     * @return Inbox
     */
    public function setToAddress($toAddress) {
        $this->to_address = $toAddress;

        return $this;
    }

    /**
     * Get toAddress
     *
     * @return string
     */
    public function getToAddress() {
        return $this->to_address;
    }

    /**
     * Set ccName
     *
     * @param string $ccName
     *
     * @return Inbox
     */
    public function setCcName($ccName) {
        $this->cc_name = $ccName;

        return $this;
    }

    /**
     * Get ccName
     *
     * @return string
     */
    public function getCcName() {
        return $this->cc_name;
    }

    /**
     * Set ccAddress
     *
     * @param string $ccAddress
     *
     * @return Inbox
     */
    public function setCcAddress($ccAddress) {
        $this->cc_address = $ccAddress;

        return $this;
    }

    /**
     * Get ccAddress
     *
     * @return string
     */
    public function getCcAddress() {
        return $this->cc_address;
    }

    /**
     * Set bccName
     *
     * @param string $bccName
     *
     * @return Inbox
     */
    public function setBccName($bccName) {
        $this->bcc_name = $bccName;

        return $this;
    }

    /**
     * Get bccName
     *
     * @return string
     */
    public function getBccName() {
        return $this->bcc_name;
    }

    /**
     * Set bccAddress
     *
     * @param string $bccAddress
     *
     * @return Inbox
     */
    public function setBccAddress($bccAddress) {
        $this->bcc_address = $bccAddress;

        return $this;
    }

    /**
     * Get bccAddress
     *
     * @return string
     */
    public function getBccAddress() {
        return $this->bcc_address;
    }

    /**
     * Set textPlain
     *
     * @param string $textPlain
     *
     * @return Inbox
     */
    public function setTextPlain($textPlain) {
        $this->text_plain = $textPlain;

        return $this;
    }

    /**
     * Get textPlain
     *
     * @return string
     */
    public function getTextPlain() {
        return $this->text_plain;
    }

    /**
     * Set textHtml
     *
     * @param string $textHtml
     *
     * @return Inbox
     */
    public function setTextHtml($textHtml) {
        $this->text_html = $textHtml;

        return $this;
    }

    /**
     * Get textHtml
     *
     * @return string
     */
    public function getTextHtml() {
        return $this->text_html;
    }

    /**
     * Set filePaths
     *
     * @param string $filePaths
     *
     * @return Inbox
     */
    public function setFilePaths($filePaths) {
        $this->file_paths = $filePaths;

        return $this;
    }

    /**
     * Get filePaths
     *
     * @return string
     */
    public function getFilePaths() {
        return $this->file_paths;
    }

    /**
     * Set fileNames
     *
     * @param string $fileNames
     *
     * @return Inbox
     */
    public function setFileNames($fileNames) {
        $this->file_names = $fileNames;

        return $this;
    }

    /**
     * Get fileNames
     *
     * @return string
     */
    public function getFileNames() {
        return $this->file_names;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Inbox
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }


    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Inbox
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parent_id;
    }
}
