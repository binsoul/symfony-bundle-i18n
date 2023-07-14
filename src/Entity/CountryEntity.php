<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Country;
use BinSoul\Common\I18n\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents a country.
 */
#[ORM\Entity]
#[ORM\Table(name: 'country')]
#[ORM\UniqueConstraint(columns: ['iso2'])]
#[ORM\Index(columns: ['iso3'])]
class CountryEntity implements Country
{
    /**
     * @var int|null ID of the country
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var string ISO2 code of the country
     */
    #[ORM\Column(type: Types::STRING, length: 2)]
    private string $iso2;

    /**
     * @var string|null ISO3 code of the country
     */
    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    private ?string $iso3 = null;

    /**
     * @var string|null Numeric ISO code of the country
     */
    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    private ?string $isoNumeric = null;

    /**
     * @var string|null DSIT code of the country
     */
    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    private ?string $dsit = null;

    /**
     * @var float|string|null Latitude of the center of the country
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private float|string|null $centerLatitude = null;

    /**
     * @var float|string|null Longitude of the center of the country
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private float|string|null $centerLongitude = null;

    #[ORM\ManyToOne(targetEntity: ContinentEntity::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ContinentEntity $continent;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setIso2(string $iso2): void
    {
        $this->iso2 = $iso2;
    }

    public function getIso2(): string
    {
        return $this->iso2;
    }

    public function setIso3(?string $iso3): void
    {
        $this->iso3 = $iso3;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setIsoNumeric(?string $isoNumeric): void
    {
        $this->isoNumeric = $isoNumeric;
    }

    public function getIsoNumeric(): ?string
    {
        return $this->isoNumeric;
    }

    public function setDsit(?string $dsit): void
    {
        $this->dsit = $dsit;
    }

    public function getDsit(): ?string
    {
        return $this->dsit;
    }

    public function setCenterLongitude(?float $centerLongitude): void
    {
        $this->centerLongitude = $centerLongitude;
    }

    public function getCenterLongitude(): ?float
    {
        return $this->centerLongitude !== null ? (float) $this->centerLongitude : null;
    }

    public function setCenterLatitude(?float $centerLatitude): void
    {
        $this->centerLatitude = $centerLatitude;
    }

    public function getCenterLatitude(): ?float
    {
        return $this->centerLatitude !== null ? (float) $this->centerLatitude : null;
    }

    public function setContinent(ContinentEntity $continent): void
    {
        $this->continent = $continent;
    }

    public function getContinent(): ContinentEntity
    {
        return $this->continent;
    }

    /**
     * Returns the name of the country.
     */
    public function getName(Locale $displayLocale): string
    {
        try {
            return Countries::getName($this->getIso2(), $displayLocale->getCode('_'));
        } catch (MissingResourceException) {
            return $this->getIso2();
        }
    }
}
