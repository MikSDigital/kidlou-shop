<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use App\Service\Parameter;

class LocaleListener implements EventSubscriberInterface {
//    /**
//     * @var urlMatcher \Symfony\Component\Routing\Matcher\UrlMatcher;
//     */
//    private $urlMatcher;

    /**
     *
     * @var type Parameter
     */
    private $parameter;

    /**
     *
     * @var type string
     */
    private $newUrl;

    /**
     *
     * @var type string
     */
    private $oldUrl;

    /**
     *
     * @var type TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator, Parameter $parameter) {
        $this->parameter = $parameter;
        $this->translator = $translator;
    }

    //https://gist.github.com/kunicmarko20/02a42c76f638322d58b1def7d2e770d7
    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        // wenn nicht lang und admin
        $this->newUrl = $request->getPathInfo();
        $this->oldUrl = $request->headers->get('referer');
        // check if admin
        if ($this->getIsAdmin()) {
            return;
        }
        $locale = $this->checkLanguage();
        if ($locale === null)
            return;
        $request->setLocale($locale);
        $this->translator->setLocale($locale);
        $pathLocale = "/" . $locale . $this->newUrl;
        //We have to catch the ResourceNotFoundException
        try {
            //Try to match the path with the local prefix
            //$this->urlMatcher->match($pathLocale);
            $event->setResponse(new RedirectResponse($pathLocale));
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {

        } catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {

        }

        if (!$request->hasPreviousSession()) {
            return;
        }
    }

    static public function getSubscribedEvents() {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }

    private function checkLanguage() {
        foreach ($this->parameter->getLanguages() as $language) {
            if (preg_match_all("/\/$language\//", $this->newUrl))
                return null;
            if (preg_match_all("/\/$language\//", $this->oldUrl))
                return $language;
        }
        return $this->parameter->getLocale();
    }

    private function getIsAdmin() {
        $pos = strpos($this->newUrl, '/' . $this->parameter->getAdminPathName() . '/');
        if ($pos !== false) {
            if ($pos == 0) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
