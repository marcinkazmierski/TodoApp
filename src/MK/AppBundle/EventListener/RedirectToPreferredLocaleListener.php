<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MK\AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * When visiting the homepage, this listener redirects the user to the most
 * appropriate localized version according to the browser settings.
 *
 * See http://symfony.com/doc/current/components/http_kernel/introduction.html#the-kernel-request-event
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class RedirectToPreferredLocaleListener
{

    /**
     * List of supported locales.
     *
     * @var string[]
     */
    private $locales = array();

    /**
     * @var string
     */
    private $defaultLocale = 'pl';

    /**
     * Constructor.
     *
     * @param string $locales Supported locales separated by '|'
     * @param string|null $defaultLocale
     */
    public function __construct($locales, $defaultLocale = null)
    {
        $this->locales = explode('|', trim($locales));
        if (empty($this->locales)) {
            throw new \UnexpectedValueException('The list of supported locales must not be empty.');
        }

        $this->defaultLocale = $defaultLocale ?: $this->locales[0];

        if (!in_array($this->defaultLocale, $this->locales)) {
            throw new \UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".', $this->defaultLocale, $locales));
        }

        array_unshift($this->locales, $this->defaultLocale);
        $this->locales = array_unique($this->locales);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /*
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $locateRedirect = false;

        if (!$locale = $request->attributes->get('_locale')) {
            $locateRedirect = true;

        }

        if (!in_array($locale, $this->locales)) {
            $locale = $this->defaultLocale;
            $locateRedirect = true;
        }
        $request->setLocale($locale);
        if ($locateRedirect) {
            $params = $request->query->all();
            $event->setResponse(new RedirectResponse($request->getBaseUrl() . '/' . $locale . '/' . ($params ? '?' . http_build_query($params) : '')));
        }
        */
    }
}
