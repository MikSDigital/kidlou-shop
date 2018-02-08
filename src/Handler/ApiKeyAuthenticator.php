<?php

namespace App\Handler;

//use App\Security\ApiKeyUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface {

    public function createToken(Request $request, $providerKey) {
        $apiKey = $request->query->get('apiKey');

        return new PreAuthenticatedToken(
                'anon.', $apiKey, $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        $apiKey = $token->getCredentials();
        $username = $userProvider->getUsernameForApiKey($apiKey);

        // The part where we try and keep the user in the session!
        $user = $token->getUser();
        if ($user instanceof ApiKeyUser) {
            return new PreAuthenticatedToken(
                    $user, $apiKey, $providerKey, $user->getRoles()
            );
        }


        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
                $user, $apiKey, $providerKey, $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

}
