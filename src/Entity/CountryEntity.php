<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Country;
use BinSoul\Common\I18n\Locale;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents a country.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="country",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"iso2"}),
 *     },
 *     indexes={
 *         @ORM\Index(columns={"iso3"}),
 *     },
 * )
 */
class CountryEntity implements Country
{
    /**
     * @var int|null ID of the country
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string ISO2 code of the country
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $iso2;

    /**
     * @var string|null ISO3 code of the country
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso3;

    /**
     * @var string|null Numeric ISO code of the country
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $isoNumeric;

    /**
     * @var string|null DSIT code of the country
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $dsit;

    /**
     * @var float|string|null Latitude of the center of the country
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $centerLatitude;

    /**
     * @var float|string|null Longitude of the center of the country
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $centerLongitude;

    /**
     * @var ContinentEntity
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\I18n\Entity\ContinentEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $continent;

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
        } catch (MissingResourceException $e) {
            return $this->getIso2();
        }
    }
}
