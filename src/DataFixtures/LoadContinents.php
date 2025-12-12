<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DataFixtures;

use BinSoul\Symfony\Bundle\I18n\Entity\ContinentEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;

class LoadContinents extends Fixture implements FixtureGroupInterface
{
    /**
     * @var array[]
     */
    private const ROWS = [
        [1, 'AF', '002', 0.000000, 0.000000],
        [2, 'AN', 'AQ', 0.000000, 0.000000],
        [3, 'AS', '142', 0.000000, 0.000000],
        [4, 'EU', '150', 0.000000, 0.000000],
        [5, 'NA', '003', 0.000000, 0.000000],
        [6, 'OC', '009', 0.000000, 0.000000],
        [7, 'SA', '005', 0.000000, 0.000000],
    ];

    public function load(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData(ContinentEntity::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::ROWS as $row) {
            $entity = new ContinentEntity($row[0]);
            $entity->setCode($row[1]);
            $entity->setCountryCode($row[2]);
            $entity->setCenterLongitude($row[3]);
            $entity->setCenterLatitude($row[4]);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['binsoul/symfony-bundle-i18n'];
    }
}
