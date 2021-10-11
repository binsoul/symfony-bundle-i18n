<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DateTimeFormatter as CommonDateTimeFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Intl\IntlDateTimeFormatter;
use BinSoul\Common\I18n\Locale;
use DateTimeInterface;

/**
 * Formats dates and times using the {@see IntlDateTimeFormatter} class.
 */
class DateTimeFormatter implements CommonDateTimeFormatter
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var IntlDateTimeFormatter
     */
    private $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->formatter = new IntlDateTimeFormatter($this->locale);
    }

    public function formatPattern(DateTimeInterface $datetime, string $pattern): string
    {
        return $this->formatter->formatPattern($datetime, $pattern);
    }

    public function formatTime(DateTimeInterface $time): string
    {
        return $this->formatter->formatTime($time);
    }

    public function formatTimeWithSeconds(DateTimeInterface $time): string
    {
        return $this->formatter->formatTimeWithSeconds($time);
    }

    public function formatDate(DateTimeInterface $date): string
    {
        return $this->formatter->formatDate($date);
    }

    public function formatDateTime(DateTimeInterface $datetime): string
    {
        return $this->formatter->formatDateTime($datetime);
    }

    public function formatDateTimeWithSeconds(DateTimeInterface $datetime): string
    {
        return $this->formatter->formatTimeWithSeconds($datetime);
    }

    public function withLocale(Locale $locale): CommonDateTimeFormatter
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }

    public function getTimePattern(): string
    {
        return $this->formatter->getTimePattern();
    }

    public function getTimeWithSecondsPattern(): string
    {
        return $this->formatter->getDateTimeWithSecondsPattern();
    }

    public function getDatePattern(): string
    {
        return $this->formatter->getDatePattern();
    }

    public function getDateTimePattern(): string
    {
        return $this->formatter->getDateTimePattern();
    }

    public function getDateTimeWithSecondsPattern(): string
    {
        return $this->formatter->getDateTimeWithSecondsPattern();
    }
}
