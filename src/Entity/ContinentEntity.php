<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents a continent.
 */
#[ORM\Entity]
#[ORM\Table(name: 'continent')]
#[ORM\UniqueConstraint(columns: ['code'])]
#[ORM\Cache(usage: 'READ_ONLY')]
class ContinentEntity
{
    /**
     * @var int|null ID of the continent
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var string Code of the continent
     */
    #[ORM\Column(type: Types::STRING, length: 2)]
    private string $code;

    /**
     * @var string Country code of the continent
     */
    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $countryCode;

    /**
     * @var string|null Latitude of the center of the continent
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private ?string $centerLatitude = null;

    /**
     * @var string|null Longitude of the center of the continent
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private ?string $centerLongitude = null;

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
        $this->centerLatitude = $this->floatToDecimal($centerLatitude);
    }

    public function getCenterLatitude(): ?float
    {
        return $this->decimalToFloat($this->centerLatitude);
    }

    public function setCenterLongitude(?float $centerLongitude): void
    {
        $this->centerLongitude = $this->floatToDecimal($centerLongitude);
    }

    public function getCenterLongitude(): ?float
    {
        return $this->decimalToFloat($this->centerLongitude);
    }

    /**
     * Returns the name of the continent.
     */
    public function getName(Locale $displayLocale): string
    {
        try {
            return Countries::getName($this->getCountryCode(), $displayLocale->getCode('_'));
        } catch (MissingResourceException) {
            return $this->getCode();
        }
    }

    private function decimalToFloat(?string $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }

    private function floatToDecimal(?float $value, int $precision = 10, int $scale = 6): ?string
    {
        if ($value === null) {
            return null;
        }

        $rounded = round($value, $scale);
        $maxValue = (10 ** ($precision - $scale)) - (10 ** (-$scale));

        if (abs($rounded) > $maxValue) {
            throw new InvalidArgumentException(sprintf(
                'The value %s does not fit into DECIMAL(%d,%d).',
                number_format($rounded, $scale, '.', ''),
                $precision,
                $scale
            ));
        }

        return number_format($rounded, $scale, '.', '');
    }
}
