<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DataFixtures;

use BinSoul\Symfony\Bundle\I18n\Entity\CurrencyEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;

class LoadCurrencies extends Fixture implements FixtureGroupInterface
{
    /**
     * @var array[]
     */
    private const ROWS = [
        [1, 'EUR', 978],
        [2, 'PLN', 985],
        [3, 'NOK', 578],
        [4, 'DKK', 208],
        [5, 'SEK', 752],
        [6, 'CHF', 756],
        [7, 'AUD', 36],
        [8, 'AWG', 533],
        [9, 'AZN', 944],
        [10, 'BAM', 977],
        [11, 'BBD', 52],
        [12, 'BDT', 50],
        [13, 'BGN', 975],
        [14, 'BHD', 48],
        [15, 'BIF', 108],
        [16, 'BMD', 60],
        [17, 'BND', 96],
        [18, 'BOB', 68],
        [19, 'BOV', 984],
        [20, 'BRL', 986],
        [21, 'BSD', 44],
        [22, 'BTN', 64],
        [23, 'BWP', 72],
        [24, 'BYR', 974],
        [25, 'BZD', 84],
        [26, 'CAD', 124],
        [27, 'CDF', 976],
        [28, 'CHE', 947],
        [30, 'CHW', 948],
        [31, 'CLF', 990],
        [32, 'CLP', 152],
        [33, 'CNY', 156],
        [34, 'COP', 170],
        [36, 'CRC', 188],
        [37, 'CUP', 192],
        [38, 'CVE', 132],
        [39, 'CZK', 203],
        [40, 'DJF', 262],
        [42, 'DOP', 214],
        [43, 'DZD', 12],
        [44, 'EGP', 818],
        [45, 'ERN', 232],
        [46, 'ETB', 230],
        [48, 'FJD', 242],
        [49, 'FKP', 238],
        [50, 'GBP', 826],
        [51, 'GEL', 981],
        [52, 'GHS', 936],
        [53, 'GIP', 292],
        [54, 'GMD', 270],
        [55, 'GNF', 324],
        [56, 'GTQ', 320],
        [57, 'GYD', 328],
        [58, 'HKD', 344],
        [59, 'HNL', 340],
        [60, 'HRK', 191],
        [61, 'HTG', 332],
        [62, 'HUF', 348],
        [63, 'IDR', 360],
        [64, 'ILS', 376],
        [65, 'INR', 356],
        [66, 'IQD', 368],
        [67, 'IRR', 364],
        [68, 'ISK', 352],
        [69, 'JMD', 388],
        [70, 'JOD', 400],
        [71, 'JPY', 392],
        [72, 'KES', 404],
        [73, 'KGS', 417],
        [74, 'KHR', 116],
        [75, 'KMF', 174],
        [76, 'KPW', 408],
        [77, 'KRW', 410],
        [78, 'KWD', 414],
        [79, 'KYD', 136],
        [80, 'KZT', 398],
        [81, 'LAK', 418],
        [82, 'LBP', 422],
        [83, 'LKR', 144],
        [84, 'LRD', 430],
        [85, 'LSL', 426],
        [86, 'LTL', 440],
        [87, 'LVL', 428],
        [88, 'LYD', 434],
        [89, 'MAD', 504],
        [90, 'MDL', 498],
        [91, 'MGA', 969],
        [92, 'MKD', 807],
        [93, 'MMK', 104],
        [94, 'MNT', 496],
        [95, 'MOP', 446],
        [96, 'MRO', 478],
        [97, 'MUR', 480],
        [98, 'MVR', 462],
        [99, 'MWK', 454],
        [100, 'MXN', 484],
        [101, 'MXV', 979],
        [102, 'MYR', 458],
        [103, 'MZN', 943],
        [104, 'NAD', 516],
        [105, 'NGN', 566],
        [106, 'NIO', 558],
        [108, 'NPR', 524],
        [109, 'NZD', 554],
        [110, 'OMR', 512],
        [111, 'PAB', 590],
        [112, 'PEN', 604],
        [113, 'PGK', 598],
        [114, 'PHP', 608],
        [115, 'PKR', 586],
        [117, 'PYG', 600],
        [118, 'QAR', 634],
        [119, 'RON', 946],
        [120, 'RSD', 941],
        [121, 'RUB', 643],
        [122, 'RWF', 646],
        [123, 'SAR', 682],
        [124, 'SBD', 90],
        [125, 'SCR', 690],
        [126, 'SDG', 938],
        [128, 'SGD', 702],
        [129, 'SHP', 654],
        [130, 'SLL', 694],
        [131, 'SOS', 706],
        [132, 'SRD', 968],
        [133, 'SSP', 225],
        [134, 'STD', 678],
        [135, 'SVC', 222],
        [136, 'SYP', 760],
        [137, 'SZL', 748],
        [138, 'THB', 764],
        [139, 'TJS', 972],
        [140, 'TMT', 934],
        [141, 'TND', 788],
        [142, 'TOP', 776],
        [143, 'TRY', 949],
        [144, 'TTD', 780],
        [145, 'TWD', 901],
        [146, 'TZS', 834],
        [147, 'UAH', 980],
        [148, 'UGX', 800],
        [149, 'USD', 840],
        [151, 'UYU', 858],
        [152, 'UZS', 860],
        [153, 'VEF', 937],
        [154, 'VND', 704],
        [155, 'VUV', 548],
        [156, 'WST', 882],
        [157, 'XAF', 950],
        [158, 'XCD', 951],
        [159, 'XOF', 952],
        [160, 'XPF', 953],
        [161, 'YER', 886],
        [162, 'ZAR', 710],
        [163, 'ZMW', 967],
        [164, 'ZWR', 935],
        [1001, 'AED', 784],
        [1002, 'AFN', 971],
        [1003, 'ALL', 8],
        [1004, 'AMD', 51],
        [1005, 'AOA', 973],
        [1006, 'ARS', 32],
    ];

    public function load(ObjectManager $manager): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $manager->getClassMetadata(CurrencyEntity::class);
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::ROWS as $row) {
            $entity = new CurrencyEntity($row[0]);
            $entity->setIso3($row[1]);
            $entity->setIsoNumeric($row[2]);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['binsoul/symfony-bundle-i18n'];
    }
}
