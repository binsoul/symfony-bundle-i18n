<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\I18n\Entity\ContinentEntity;
use Doctrine\Common\Persistence\ManagerRegistry;

class ContinentRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(ContinentEntity::class, $registry);
    }

    /**
     * @return ContinentEntity[]
     */
    public function loadAll(): array
    {
        /** @var ContinentEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?ContinentEntity
    {
        /** @var ContinentEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function findByCode(string $code): ?ContinentEntity
    {
        /** @var ContinentEntity|null $result */
        $result = $this->getRepository()->findOneBy(['code' => $code]);

        return $result;
    }
}
