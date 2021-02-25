<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BinSoul\Symfony\Bundle\I18n\Translation\Loader\MessageRepositoryLoader;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

/**
 * Replaces the original Symfony FrameworkBundle translator.
 */
class DatabaseTranslator extends BaseTranslator
{
    /**
     * @var MessageRepositoryLoader
     */
    private $messageRepositoryLoader;

    /**
     * @var MessageFormatterInterface
     */
    private $messageFormatter;

    public function __construct(
        ContainerInterface $container,
        MessageFormatterInterface $formatter,
        string $defaultLocale,
        array $loaderIds = [],
        array $options = [],
        array $enabledLocales = []
    ) {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options, $enabledLocales);

        $this->messageRepositoryLoader = $container->get(MessageRepositoryLoader::class);
        $this->messageFormatter = $formatter;
    }

    public function addResource(string $format, $resource, string $locale, ?string $domain = null): void
    {
        parent::addResource($format, $resource, $locale, $domain);

        if ($domain !== null && $domain !== 'messages') {
            // add an additional resource to trigger the database loader
            parent::addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
        }
    }

    public function trans($id, array $parameters = array(), $domain = null, $locale = null){
        if ($id === null || $id === '') {
            return '';
        }

        if ($domain === null) {
            $domain = 'messages';
        }

        $result = parent::trans($id, $parameters, $domain, $locale);

        $isUntranslated = $result === $id || mb_strtolower($result) === mb_strtolower($id);

        if ($isUntranslated) {
            $catalogue = $this->messageRepositoryLoader->load('', $locale ?? 'de_DE', $domain);
            $locale = $catalogue->getLocale();
            while (!$catalogue->defines($id, $domain)) {
                $fallbackCatalogue = $catalogue->getFallbackCatalogue();
                if ($fallbackCatalogue === null) {
                    break;
                }

                $catalogue = $fallbackCatalogue;
                $locale = $catalogue->getLocale();
            }

            $result = $this->messageFormatter->format($catalogue->get($id, $domain), $locale ?? 'de_DE', $parameters);
        }

        return $result;
    }
}
