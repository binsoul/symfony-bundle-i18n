<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\EventListener;

use BinSoul\Symfony\Bundle\I18n\I18nManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigGlobalsListener implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var I18nManager
     */
    private $i18nManager;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(Environment $twig, I18nManager $i18nManager)
    {
        $this->twig = $twig;
        $this->i18nManager = $i18nManager;
    }

    /**
     * @return mixed[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onController', -150],
            ],
        ];
    }

    public function onController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('i18nManager', $this->i18nManager);
    }
}
