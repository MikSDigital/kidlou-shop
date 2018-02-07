<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use App\Service\Parameter;

class LocaleListener implements EventSubscriberInterface {

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

        if ($this->getIsApi()) {
            return;
        }

        $locale = $this->checkLanguage();
        if ($locale === null) {
            return;
        }
        // check is is web_profile
        if ($this->getIsWebProfile()) {
            return;
        }

        $request->setLocale($locale);
        $this->translator->setLocale($locale);
        $pathLocale = "/" . $locale . $this->newUrl;

        //We have to catch the ResourceNotFoundException
        try {
            $event->setResponse(new RedirectResponse($pathLocale));
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {

        } catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {

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
        if (0 === strpos($this->newUrl, '/' . $this->parameter->getAdminPathName() . '/')) {
            return TRUE;
        }
        if (0 === strpos($this->oldUrl, '/' . $this->parameter->getAdminPathName() . '/')) {
            return TRUE;
        }
        return FALSE;
    }

    private function getIsApi() {
        if (0 === strpos($this->newUrl, '/' . $this->parameter->getApiPathName() . '/')) {
            return TRUE;
        }
        if (0 === strpos($this->oldUrl, '/' . $this->parameter->getApiPathName() . '/')) {
            return TRUE;
        }
        return FALSE;
    }

    private function getIsWebProfile() {
        if (0 === strpos($this->newUrl, "/_wdt") || 0 === strpos($this->newUrl, "/_profiler")) {
            return TRUE;
        }
        return FALSE;
    }

}
