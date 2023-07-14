<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Currency;
use BinSoul\Common\I18n\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents a currency.
 */
#[ORM\Entity]
#[ORM\Table(name: 'currency')]
#[ORM\UniqueConstraint(columns: ['iso3'])]
class CurrencyEntity implements Currency
{
    /**
     * @var int|null ID of the currency
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var string ISO3 code of the currency
     */
    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $iso3;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private ?string $isoNumeric = null;

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

    public function setIso3(string $iso3): void
    {
        $this->iso3 = $iso3;
    }

    public function getIso3(): string
    {
        return $this->iso3;
    }

    public function setIsoNumeric(int $value): void
    {
        $this->isoNumeric = (string) $value;
    }

    public function getIsoNumeric(): int
    {
        return (int) $this->isoNumeric;
    }

    /**
     * Returns the name of the currency.
     */
    public function getName(Locale $displayLocale): string
    {
        try {
            return Currencies::getName($this->getIso3(), $displayLocale->getCode('_'));
        } catch (MissingResourceException) {
            return $this->getIso3();
        }
    }
}
