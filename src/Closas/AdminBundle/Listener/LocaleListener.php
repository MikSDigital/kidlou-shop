<?php

namespace Closas\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LocaleListener implements EventSubscriberInterface {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
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
