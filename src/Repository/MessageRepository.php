<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use BinSoul\Symfony\Bundle\I18n\Entity\MessageEntity;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MessageRepository extends AbstractRepository
{
    private readonly ?CacheItemPoolInterface $messageCache;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry, ?CacheItemPoolInterface $messageCache = null)
    {
        parent::__construct(MessageEntity::class, $registry);

        $this->messageCache = $messageCache;
    }

    /**
     * @return MessageEntity[]
     */
    public function loadAll(): array
    {
        /** @var MessageEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?MessageEntity
    {
        /** @var MessageEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    /**
     * Returns all messages for the given locale.
     *
     * @return MessageEntity[]
     */
    public function findAllByLocale(LocaleEntity $locale): array
    {
        /** @var MessageEntity[] $result */
        $result = $this->getRepository()->findBy(['locale' => $locale]);

        return $result;
    }

    /**
     * Returns all messages for the given locale and the given domain.
     *
     * @return MessageEntity[]
     */
    public function findAllByLocaleAndDomain(LocaleEntity $locale, string $domain): array
    {
        if ($this->messageCache !== null) {
            $key = str_replace(str_split(ItemInterface::RESERVED_CHARACTERS), '-', __CLASS__ . '_locale_' . $locale->getCode() . '_domain_' . $domain);
            $item = $this->messageCache->getItem($key);

            if ($item->isHit()) {
                return $item->get();
            }
        }

        /** @var MessageEntity[] $result */
        $result = $this->getRepository()->findBy(['locale' => $locale, 'domain' => $domain]);

        if ($this->messageCache !== null) {
            $item->set($result);
            $item->expiresAfter(3600);

            $this->messageCache->save($item);
        }

        return $result;
    }
}
