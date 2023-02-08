<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Language;
use BinSoul\Common\I18n\Locale;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Intl\Locales;

/**
 * Represents a language.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="language",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"iso2"}),
 *         @ORM\UniqueConstraint(columns={"iso3"}),
 *     },
 * )
 */
class LanguageEntity implements Language
{
    /**
     * @var int|null ID of the language
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string ISO2 code of the language
     *
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $iso2;

    /**
     * @var string ISO3 code of the language
     *
     * @ORM\Column(type="string", length=3, nullable=false)
     */
    private $iso3;

    /**
     * @var string Directionality of the language
     *
     * @ORM\Column(type="string", length=3, nullable=false)
     */
    private $directionality;

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

    public function setIso3(string $iso3): void
    {
        $this->iso3 = $iso3;
    }

    public function getIso3(): string
    {
        return $this->iso3;
    }

    public function setDirectionality(string $directionality): void
    {
        $this->directionality = $directionality;
    }

    public function getDirectionality(): string
    {
        return $this->directionality;
    }

    /**
     * Returns the name of the language.
     */
    public function getName(?Locale $displayLocale = null): string
    {
        if ($displayLocale !== null) {
            try {
                return Locales::getName($this->getIso2(), $displayLocale->getCode('_'));
            } catch (MissingResourceException $e) {
                // ignore
            }
        }

        try {
            return Languages::getName($this->getIso2(), $this->getIso2());
        } catch (MissingResourceException $e) {
            return $this->getIso3();
        }
    }
}
