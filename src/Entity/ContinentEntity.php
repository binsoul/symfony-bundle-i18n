<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Locale;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents a continent.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="continent",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(columns={"code"})
 *     },
 * )
 */
class ContinentEntity
{
    /**
     * @var int|null ID of the continent
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Code of the continent
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $code;

    /**
     * @var string Country code of the continent
     * @ORM\Column(type="string", length=3, nullable=false)
     */
    private $countryCode;

    /**
     * @var float|string|null Latitude of the center of the continent
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $centerLatitude;

    /**
     * @var float|string|null Longitude of the center of the continent
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $centerLongitude;

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

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCenterLatitude(?float $centerLatitude): void
    {
        $this->centerLatitude = $centerLatitude;
    }

    public function getCenterLatitude(): ?float
    {
        return $this->centerLatitude !== null ? (float) $this->centerLatitude : null;
    }

    public function setCenterLongitude(?float $centerLongitude): void
    {
        $this->centerLongitude = $centerLongitude;
    }

    public function getCenterLongitude(): ?float
    {
        return $this->centerLongitude !== null ? (float) $this->centerLongitude : null;
    }

    /**
     * Returns the name of the continent.
     */
    public function getName(Locale $displayLocale): string
    {
        try {
            return Countries::getName($this->getCountryCode(), $displayLocale->getCode('_'));
        } catch (MissingResourceException $e) {
            return $this->getCode();
        }
    }
}
