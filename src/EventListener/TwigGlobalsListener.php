<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\EventListener;

use BinSoul\Symfony\Bundle\I18n\I18nManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

readonly class TwigGlobalsListener implements EventSubscriberInterface
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(private Environment $twig, private I18nManager $i18nManager)
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onController', -150],
            ],
        ];
    }

    public function onController(): void
    {
        $this->twig->addGlobal('i18nManager', $this->i18nManager);
    }
}
