<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use Doctrine\Persistence\ManagerRegistry;

class LocaleRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(LocaleEntity::class, $registry);
    }

    /**
     * @return LocaleEntity[]
     */
    public function loadAll(): array
    {
        /** @var LocaleEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?LocaleEntity
    {
        /** @var LocaleEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function findByCode(string $code): ?LocaleEntity
    {
        /** @var LocaleEntity|null $result */
        $result = $this->getRepository()->findOneBy(['code' => $code]);

        return $result;
    }

    /**
     * Returns the parent of the given locale or null if no parent exists.
     */
    public function findParent(LocaleEntity $locale): ?LocaleEntity
    {
        $parsedLocale = DefaultLocale::fromString($locale->getCode());
        $parentLocale = $parsedLocale->getParent();
        if ($parentLocale->isRoot()) {
            return null;
        }

        return $this->findByCode($parentLocale->getCode());
    }
}
