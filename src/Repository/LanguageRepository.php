<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\LanguageEntity;
use Doctrine\Persistence\ManagerRegistry;

class LanguageRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(LanguageEntity::class, $registry);
    }

    /**
     * @return LanguageEntity[]
     */
    public function loadAll(): array
    {
        /** @var LanguageEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?LanguageEntity
    {
        /** @var LanguageEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function findByIso2(string $iso2): ?LanguageEntity
    {
        /** @var LanguageEntity|null $result */
        $result = $this->getRepository()->findOneBy(['iso2' => $iso2]);

        return $result;
    }

    public function findByIso3(string $iso3): ?LanguageEntity
    {
        /** @var LanguageEntity|null $result */
        $result = $this->getRepository()->findOneBy(['iso3' => $iso3]);

        return $result;
    }
}
