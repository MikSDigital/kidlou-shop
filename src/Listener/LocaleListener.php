<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use App\Service\Language;

class LocaleListener implements EventSubscriberInterface {

    private $language;

    public function __construct(TranslatorInterface $translator, Language $language) {
        $this->language = $language;
    }

    //https://gist.github.com/kunicmarko20/02a42c76f638322d58b1def7d2e770d7
    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
//        echo $request->getPathInfo();
//        exit;
        if (!$request->hasPreviousSession()) {
            return;
        }

        if (strlen($request->get('lang')) && strpos($request->get('_route'), 'admin_') !== false) {
            $request->setLocale($request->get('lang'));
            $this->translator->setLocale($request->get('lang'));
        }

        if (strlen($request->getSession()->get('_locale'))) {
            $request->setLocale($request->getSession()->get('_locale'));
            $this->translator->setLocale($request->getSession()->get('_locale'));
        }
    }

    static public function getSubscribedEvents() {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }

}
