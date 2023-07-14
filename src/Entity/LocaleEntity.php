<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;

/**
 * Represents a locale.
 */
#[ORM\Table(name: 'locale')]
#[ORM\UniqueConstraint(columns: ['code'])]
#[ORM\Entity]
class LocaleEntity implements Locale
{
    /**
     * @var int|null ID of the locale
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var string Code of the locale
     */
    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $code;

    /**
     * @var LanguageEntity Language of the locale
     */
    #[ORM\ManyToOne(targetEntity: LanguageEntity::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private LanguageEntity $language;

    /**
     * @var CountryEntity|null Country of the locale
     */
    #[ORM\ManyToOne(targetEntity: CountryEntity::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?CountryEntity $country = null;

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

    public function getCode(string $separator = '-'): string
    {
        if ($separator === '-') {
            return $this->code;
        }

        return DefaultLocale::fromString($this->code)->getCode($separator);
    }

    public function setLanguage(LanguageEntity $language): void
    {
        $this->language = $language;
    }

    public function getLanguage(): LanguageEntity
    {
        return $this->language;
    }

    public function setCountry(?CountryEntity $country = null): void
    {
        $this->country = $country;
    }

    public function getCountry(): ?CountryEntity
    {
        return $this->country;
    }

    /**
     * Returns the name of the locale.
     */
    public function getName(?Locale $displayLocale = null): string
    {
        $localeCode = $this->getCode('_');

        if ($displayLocale !== null) {
            try {
                return Locales::getName($localeCode, $displayLocale->getCode('_'));
            } catch (MissingResourceException) {
                // ignore
            }
        }

        try {
            return Locales::getName($localeCode, $localeCode);
        } catch (MissingResourceException) {
            return $this->code;
        }
    }
}
