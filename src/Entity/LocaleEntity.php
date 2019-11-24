<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;

/**
 * Represents a locale.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="system_locale",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(columns={"code"})
 *     },
 * )
 */
class LocaleEntity implements Locale
{
    /**
     * @var int|null ID of the locale
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Code of the locale
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $code;

    /**
     * @var LanguageEntity Language of the locale
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\I18n\Entity\LanguageEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @var CountryEntity|null Country of the locale
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\I18n\Entity\CountryEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $country;

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
    public function getName(Locale $displayLocale): string
    {
        try {
            return Locales::getName($this->getCode('_'), $displayLocale->getCode('_'));
        } catch (MissingResourceException $e) {
            return $this->code;
        }
    }
}
