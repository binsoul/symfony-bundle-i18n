<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use BinSoul\Symfony\Bundle\I18n\Entity\MessageEntity;
use Doctrine\Common\Persistence\ManagerRegistry;

class MessageRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(MessageEntity::class, $registry);
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
        /** @var MessageEntity[] $result */
        $result = $this->getRepository()->findBy(['locale' => $locale, 'domain' => $domain]);

        return $result;
    }
}
