<?php

namespace Closas\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="admin_user")
 * @ORM\Entity(repositoryClass="Closas\AdminBundle\Entity\Repository\User")
 */
class User implements AdvancedUserInterface, \Serializable {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 6,
     *      max = 8,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25)
     */
    private $first_name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25)
     */
    private $last_name;

    /**
     * @ORM\OneToMany(targetEntity="Closas\ShopBundle\Entity\Content", mappedBy="user")
     */
    private $contents;

    public function __construct() {
        $this->contents = new ArrayCollection();
    }

    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return array('ROLE_ADMIN');
    }

    public function eraseCredentials() {

    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }

    public function getUsername() {
        return $this->username;
    }

    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
                // see section on salt below
                // $this->salt,
        ));
    }

    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->password,
                $this->isActive,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName) {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName) {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }


    /**
     * Add content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     *
     * @return User
     */
    public function addContent(\Closas\ShopBundle\Entity\Content $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \Closas\ShopBundle\Entity\Content $content
     */
    public function removeContent(\Closas\ShopBundle\Entity\Content $content)
    {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
    }
}
