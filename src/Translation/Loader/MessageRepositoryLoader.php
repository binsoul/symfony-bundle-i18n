<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation\Loader;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\Repository\LocaleRepository;
use BinSoul\Symfony\Bundle\I18n\Repository\MessageRepository;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loads translations using the {@see MessageRepository}.
 */
class MessageRepositoryLoader implements LoaderInterface
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(MessageRepository $messageRepository, LocaleRepository $localeRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->localeRepository = $localeRepository;
    }

    public function load($resource, $locale, $domain = 'messages')
    {
        $localeEntity = null;
        $parsedLocale = DefaultLocale::fromString($locale, '_');
        while (!$parsedLocale->isRoot()) {
            $localeEntity = $this->localeRepository->findByCode($parsedLocale->getCode('-'));
            if ($localeEntity !== null) {
                break;
            }

            $parsedLocale = $parsedLocale->getParent();
        }

        if ($localeEntity === null) {
            throw new NotFoundResourceException(sprintf('The locale "%s" does not exist.', $locale));
        }

        $messages = [];
        $entities = $this->messageRepository->findAllByLocaleAndDomain($localeEntity, $domain);
        foreach ($entities as $entity) {
            $messages[$entity->getKey()] = $entity->getFormat();
        }

        $catalogue = new MessageCatalogue($locale);
        $catalogue->add($messages, $domain);

        return $catalogue;
    }
}
