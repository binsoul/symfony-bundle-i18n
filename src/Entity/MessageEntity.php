<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Entity;

use BinSoul\Common\I18n\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a translatable message.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="system_message",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(columns={"locale_id", "key", "domain"})
 *     },
 *     indexes={
 *        @ORM\Index(columns={"locale_id", "domain"})
 *     },
 * )
 */
class MessageEntity implements Message
{
    /**
     * @var int|null ID of the message
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Key of the message
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private $key;

    /**
     * @var string Format of the message
     * @ORM\Column(type="string", length=8192, nullable=false)
     */
    private $format;

    /**
     * @var string Domain of the message
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private $domain = 'messages';

    /**
     * @var LocaleEntity Locale of the message
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $locale;

    /**
     * @var mixed[] Parameters of the message
     */
    private $parameters;

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

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getLocale(): LocaleEntity
    {
        return $this->locale;
    }

    public function setLocale(LocaleEntity $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
