<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\CurrencyEntity;
use Doctrine\Common\Persistence\ManagerRegistry;

class CurrencyRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(CurrencyEntity::class, $registry);
    }

    /**
     * @return CurrencyEntity[]
     */
    public function loadAll(): array
    {
        /** @var CurrencyEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?CurrencyEntity
    {
        /** @var CurrencyEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function findByIso3(string $iso3): ?CurrencyEntity
    {
        /** @var CurrencyEntity|null $result */
        $result = $this->getRepository()->findOneBy(['iso3' => $iso3]);

        return $result;
    }
}
