<?php

namespace App\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface {

    /**
     *
     * @var container $container
     */
    private $container;

    /**
     *
     * @var router $router
     */
    private $router;

    public function __construct(Container $container, $router) {
        $this->container = $container;
        $this->router = $router;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $referer = $request->headers->get('referer');
        $this->setZoneToSession($request);
        $this->container->get('session')->getFlashBag()->add('error', $exception->getMessage());
        $this->container->get('session')->set('login_failed', TRUE);
        return new RedirectResponse($referer);
    }

    public function onLogoutSuccess(Request $request) {
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $this->container->get('session')->set('login_failed', FALSE);
        // Dies wird für das speichern der Zone parameters gebraucht,
        // wird später gebraucht wenn der user mit seinem Login sich anmelden wird
        $this->setZoneToSession($request);

        // if current route is equal referer route than is index login area
        if (strpos($request->headers->get('referer'), $this->router->generate($request->get('_route')))) {
            return new RedirectResponse($this->router->generate('user_index'));
        } else {
            $referer = $request->headers->get('referer');
            $baseUrl = $request->getSchemeAndHttpHost();
            $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));
            if ($request->get('_target_path')) {
                $lastPath = $request->get('_target_path');
            }
            return new RedirectResponse($lastPath);
        }
    }

    /**
     *
     * @param type $request
     */
    private function setZoneToSession($request) {
        if ($request->request->get('zone-plz') && $request->request->get('zone-city')) {
            $this->container->get('session')->set('zone-plz', $request->request->get('zone-plz'));
            $this->container->get('session')->set('zone-city', $request->request->get('zone-city'));
        }
    }

}
