<?php

// src/Security/ApiKeyUserProvider.php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use App\Entity\ApiUser;

class ApiKeyUserProvider implements UserProviderInterface {

    protected $userApiRepository;

    public function __construct(EntityRepository $userApiRepository) {
        //$this->authTokenRepository = $authTokenRepository;
        $this->userApiRepository = $userApiRepository;
    }

    public function getUsernameForApiKey($apiKey) {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        return $this->userApiRepository->findOneBy(array('apiKey' => $apiKey))->getUsername();
    }

    public function loadUserByUsername($username) {
        return $this->userApiRepository->findOneBy(array('username' => $username));
    }

    public function refreshUser(UserInterface $user) {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class) {
        return ApiUser::class === $class;
    }

}
